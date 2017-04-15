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

use Symfony\Component\HttpFoundation\JsonResponse;
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

	/** @var \phpbb\user */
	protected $user;

	/* @var \rmcgirr83\stopforumspam\core\sfsgroups */
	protected $sfsgroups;

	public function __construct(
			\phpbb\auth\auth $auth,
			\phpbb\config\config $config,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\log\log $log,
			\phpbb\request\request $request,
			\phpbb\user $user,
			\rmcgirr83\stopforumspam\core\sfsgroups $sfsgroups)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->log = $log;
		$this->request = $request;
		$this->user = $user;
		$this->sfsgroups = $sfsgroups;
	}

	public function reporttosfs($username, $userip, $useremail, $postid, $posterid)
	{
		$admins_mods = $this->sfsgroups->getadminsmods();

		$data = array();
		// only allow this via ajax calls
		//$this->request->is_ajax() && 
		if ($this->auth->acl_gets('a_', 'm_') && (!empty($this->config['allow_sfs']) && !empty($this->config['sfs_api_key'])) && !in_array($posterid, $admins_mods))
		{
			$this->user->add_lang_ext('rmcgirr83/stopforumspam', array('stopforumspam', 'acp/acp_stopforumspam'));

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
				$http_request .= '&ip_addr=' . $userip;
				$http_request .= '&email=' . $useremail_encode;
				$http_request .= '&api_key=' . $this->config['sfs_api_key'];

				$response = $this->get_file($http_request);

				if (!$response)
				{
					$data = array(
						'MESSAGE_TITLE'	=> $this->user->lang('ERROR'),
						'MESSAGE_TEXT'	=> $this->user->lang('SFS_ERROR_MESSAGE'),
						'success'	=> false,
					);
					return new JsonResponse($data);
				}

				$sfs_username_check = $this->user->lang('SFS_USERNAME_STOPPED', $username);
				//$sfs_postid = 
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_REPORTED', time(), array($sfs_username_check));

				// Now set the post as reported
				$this->db->sql_query('UPDATE ' . POSTS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', array(
					'sfs_reported' => true,
				)) . ' WHERE post_id = ' . (int) $postid);

				$data = array(
					'MESSAGE_TITLE'	=> $this->user->lang('SUCCESS'),
					'MESSAGE_TEXT'	=> $this->user->lang('SFS_SUCCESS_MESSAGE'),
					'success'	=> true,
					'postid'	=> $postid,
				);
				return new JsonResponse($data);
			}
			throw new http_exception(404, 'SFS_NEED_CURL');
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
