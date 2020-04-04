<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace rmcgirr83\stopforumspam\core;

class sfsgroups
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\cache\service */
	protected $cache;

	public function __construct(
			\phpbb\auth\auth $auth,
			\phpbb\cache\service $cache)
	{
		$this->auth = $auth;
		$this->cache = $cache;
	}

	/*
	 * just generate a cache of users who are admins and mods
	 * this is used in the listener as well as reporttosfs files
	 */
	public function getadminsmods($forum_id)
	{
		$admins_mods = $this->cache->get('_sfs_adminsmods');

		// ensure the cache was built in the ACP
		if (!$admins_mods)
		{
			$admins_mods = array();
		}

		if ($forum_id)
		{
			// now get just the moderators of the forum
			$forum_mods = $this->auth->acl_get_list(false, 'm_', $forum_id);
			$forum_mods = (!empty($forum_mods[$forum_id]['m_'])) ? $forum_mods[$forum_id]['m_'] : array();

			// merge the arrays
			$admins_mods = array_unique(array_merge($admins_mods, $forum_mods));

		}

		return $admins_mods;
	}
}
