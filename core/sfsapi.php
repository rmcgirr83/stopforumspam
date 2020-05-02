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

class sfsapi
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(\phpbb\config\config $config, \phpbb\log\log $log, \phpbb\user $user, $root_path, $php_ext)
	{
		$this->config = $config;
		$this->log = $log;
		$this->user = $user;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/*
	 * sfsapi
	 * @param 	$type 			whether we are adding or querying
	 * @param	$username		the users name
	 * @param	$userip			the users ip
	 * @param	$useremail		the users email addy
	 * @param	$apikey			the api key of the forum
	 * @return 	string			return either a string on success or false on failure
	*/
	public function sfsapi($type, $username, $userip, $useremail, $apikey = '')
	{
		// We'll use curl..most servers have it installed as default
		if (!function_exists('curl_init'))
		{
			// no cURL no extension
			$this->config->set('allow_sfs', false);

			$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_NEED_CURL');

			return false;
		}

		if ($type == 'add')
		{
			$url = 'http://www.stopforumspam.com/add.php';
			$data = array(
				'username' => $username,
				'ip' => $userip,
				'email' => $useremail,
				'api_key' => $apikey
			);

			$data = http_build_query($data);
		}
		else
		{
			$url = 'http://www.stopforumspam.com/api';
			$data = array(
				'username' => $username,
				'email' => $useremail,
				'ip' => $userip
			);

			$data = http_build_query($data);
			$data = $data . '&nobadusername&json';
		}

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 5);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
		$contents = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		// if nothing is returned (SFS is down)
		if ($httpcode != 200)
		{
			return false;
		}

		return $contents;
	}

	// ban a nub
	public function sfs_ban($type, $user_info)
	{
		if (!function_exists('user_ban'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}

		if ($this->config['sfs_ban_ip'])
		{
			$lang_display = ($type = 'user') ? $this->user->lang['SFS_USER_BANNED'] : $this->user->lang['SFS_BANNED'];
			$ban_reason = (!empty($this->config['sfs_ban_reason'])) ? $lang_display : '';
			// ban the nub
			user_ban($type, $user_info, (int) $this->config['sfs_ban_time'], 0, false, $lang_display, $ban_reason);
		}
		return;
	}
}
