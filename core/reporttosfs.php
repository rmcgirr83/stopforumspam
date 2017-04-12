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

use phpbb\exception\http_exception;

class reporttosfs
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\log\log */
	protected $log;	

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string PHP extension */
	protected $php_ext;

	public function __construct(
			\phpbb\auth\auth $auth,
			\phpbb\config\config $config,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\log\log $log,
			\phpbb\request\request $request,
			\phpbb\template\template $template,
			\phpbb\user $user,
			$phpbb_root_path,
			$php_ext)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function reporttosfs($username, $userip, $useremail, $postid)
	{
		// only allow this via ajax calls
		if ($this->request->is_ajax() && $this->auth->acl_gets('a_', 'm_') && (!empty($this->config['allow_sfs']) && !empty($this->config['sfs_api_key'])))
		{

			$this->user->add_lang_ext('rmcgirr83/stopforumspam', 'stopforumspam');

			$sql = 'SELECT sfs_reported
				FROM ' . POSTS_TABLE . '
				WHERE ' . $this->db->sql_in_set('post_id', array( (int) $postid));
			$result = $this->db->sql_query($sql);
			$sfs_done = (int) $this->db->sql_fetchfield('sfs_reported');

			if ($sfs_done)
			{
				throw new http_exception(403, 'SFS_REPORTED');
			}

			$username_encode = urlencode($username);
			$useremail_encode = urlencode($useremail);

			// We'll use curl..most servers have it installed as default
			if (function_exists('curl_init'))
			{
				// add the spammer to the SFS database
				$http_request = 'http://www.stopforumspam.com/add.php';
				$http_request .= '?username=' . $username_encode;
				$http_request .= '&ip_addr=75.26.95.97';//' . $userip;
				$http_request .= '&email=' . $useremail_encode;
				$http_request .= '&api_key=' . $this->config['sfs_api_key'];

				$json_response = new \phpbb\json_response();
				$response = $this->get_file($http_request);

				if (!$response)
				{
					$json_response->send(array(
						'success'	=> false,
					));
				}

				$sfs_username_check = $this->user->lang('SFS_USERNAME_STOPPED', $username);
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_REPORTED', time(), array($sfs_username_check));

				// Now set the new user to have the total amount of posts.  ;)
				$this->db->sql_query('UPDATE ' . POSTS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', array(
					'sfs_reported' => true,
				)) . ' WHERE post_id = ' . (int) $postid);

				$json_response->send(array(
					'success'	=> true,
				));
			}
		}
		throw new http_exception(403, 'NOT_AUTHORISED');
	}

	// use curl to get response from SFS
	private function get_file($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
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

		return true;
	}
}
