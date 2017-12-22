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

	public function __construct(\phpbb\config\config $config, \phpbb\log\log $log, \phpbb\user $user)
	{
		$this->config = $config;
		$this->log = $log;
		$this->user = $user;
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
			$http_request = 'http://www.stopforumspam.com/add.php';
			$http_request .= '?username=' . urlencode($username);
			$http_request .= '&ip_addr=' . $userip;
			$http_request .= '&email=' . urlencode($useremail);
			$http_request .= '&api_key=' . $apikey;

		}
		else
		{
			$http_request = 'https://api.stopforumspam.org/api';
			$http_request .= '?username=' . urlencode($username);
			$http_request .= '&ip=' . $userip;
			$http_request .= '&email=' . urlencode($useremail) . '&json&nobadusername';
		}

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $http_request);
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
}
