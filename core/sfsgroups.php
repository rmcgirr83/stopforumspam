<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) 2015 Rich McGirr (RMcGirr83)
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\core;

use phpbb\auth\auth;
use phpbb\cache\service as cache;
use phpbb\config\config;

/**
* Finds the administrators and moderators who must never be reported to Stop Forum Spam
*/
class sfsgroups
{
	/** Seconds the per-forum moderator lists stay cached, so ACP permission changes are picked up */
	const FORUM_MODS_CACHE_TTL = 3600;

	/** @var auth $auth */
	protected $auth;

	/** @var cache $cache */
	protected $cache;

	/** @var config $config */
	protected $config;

	public function __construct(
			auth $auth,
			cache $cache,
			config $config)
	{
		$this->auth = $auth;
		$this->cache = $cache;
		$this->config = $config;
	}

	/**
	* getadminsmods		get the users who are admins and global mods, merged with the moderators of the given forum
	*					this is used in the listener as well as reporttosfs files
	* @param	$forum_id	the id of a forum
	* @return 	array
	* @access	public
	*/
	public function getadminsmods($forum_id)
	{
		// build the global admins/mods cache if it does not exist yet
		$admins_mods = $this->build_adminsmods_cache();

		$forum_id = (int) $forum_id;

		if ($forum_id)
		{
			$forums_mods = $this->cache->get('_sfs_forums_mods');

			if ($forums_mods === false)
			{
				$forums_mods = [];
			}

			if (!isset($forums_mods[$forum_id]))
			{
				// now get just the moderators of the forum
				$forum_mods = $this->auth->acl_get_list(false, 'm_', $forum_id);
				$forums_mods[$forum_id] = (!empty($forum_mods[$forum_id]['m_'])) ? $forum_mods[$forum_id]['m_'] : [];

				// cache for a limited time so permission changes made in the ACP are picked up
				$this->cache->put('_sfs_forums_mods', $forums_mods, self::FORUM_MODS_CACHE_TTL);
			}

			// merge the arrays
			$admins_mods = array_unique(array_merge($admins_mods, $forums_mods[$forum_id]));
		}

		return $admins_mods;
	}

	/**
	* build_adminsmods_cache		generate a cache of users who are admins and global mods
	*								this is used in the listener as well as reporttosfs/reportpms files
	* @return 	array
	* @access	public
	*/
	public function build_adminsmods_cache()
	{
		$admins_mods = $this->cache->get('_sfs_adminsmods');

		if ($admins_mods === false && !empty($this->config['sfs_api_key']))
		{
			// Grab an array of user_id's with admin permissions
			$admin_ary = $this->auth->acl_get_list(false, 'a_', false);
			$admin_ary = (!empty($admin_ary[0]['a_'])) ? $admin_ary[0]['a_'] : [];

			// Grab an array of user id's with global mod permissions
			$mod_ary = $this->auth->acl_get_list(false, 'm_', false);
			$mod_ary = (!empty($mod_ary[0]['m_'])) ? $mod_ary[0]['m_'] : [];

			$admins_mods = array_unique(array_merge($admin_ary, $mod_ary));

			// cache this data for ever
			$this->cache->put('_sfs_adminsmods', $admins_mods);
		}

		return ($admins_mods !== false) ? $admins_mods : [];
	}

	/**
	* refresh_adminsmods_cache		rebuild the admins/mods caches after a group change, but only if the group can affect them
	* @param	$group_id	the id of the group users were added to or removed from
	* @return 	void
	* @access	public
	*/
	public function refresh_adminsmods_cache($group_id)
	{
		if ($this->group_has_adminmod_permissions($group_id))
		{
			$this->rebuild_adminsmods_cache();
		}
	}

	/**
	* rebuild_adminsmods_cache		drop the cached admins/mods lists and build them again from current permissions
	* @return 	array
	* @access	public
	*/
	public function rebuild_adminsmods_cache()
	{
		$this->cache->destroy('_sfs_adminsmods');
		$this->cache->destroy('_sfs_forums_mods');

		return $this->build_adminsmods_cache();
	}

	/**
	* group_has_adminmod_permissions	check if a group is assigned the base admin or moderator permission, on any forum
	*									groups without them can never change the admins/mods lists, so e.g. joining
	*									the REGISTERED group on registration does not need a cache rebuild
	* @param	$group_id	the id of a group
	* @return 	bool
	* @access	public
	*/
	public function group_has_adminmod_permissions($group_id)
	{
		$group_auth = $this->auth->acl_group_raw_data((int) $group_id, ['a_', 'm_'], false);

		return !empty($group_auth);
	}
}
