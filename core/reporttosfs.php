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
use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbb\exception\http_exception;

class reporttosfs
{
	private $forumid = 0;
	private $topicid = 0;
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

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
			ContainerInterface $container,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\log\log $log,
			\phpbb\request\request $request,
			\phpbb\user $user,
			\rmcgirr83\stopforumspam\core\sfsgroups $sfsgroups)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->log = $log;
		$this->request = $request;
		$this->user = $user;
		$this->sfsgroups = $sfsgroups;
	}

	public function reporttosfs($username, $userip, $useremail, $postid, $posterid)
	{
		$postid = (int) $postid;
		$posterid = (int) $posterid;

		if ($postid <= 0)
		{
			throw new http_exception(403, 'NO_POST_SELECTED');
		}

		$admins_mods = $this->sfsgroups->getadminsmods();

		$data = array();
		// only allow this via ajax calls
		if ($this->request->is_ajax() && $this->auth->acl_gets('a_', 'm_') && !empty($this->config['allow_sfs']) && !empty($this->config['sfs_api_key']) && !in_array($posterid, $admins_mods) && $posterid != ANONYMOUS)
		{
			$this->user->add_lang_ext('rmcgirr83/stopforumspam', array('stopforumspam', 'acp/acp_stopforumspam'));

			$sql = 'SELECT sfs_reported
				FROM ' . POSTS_TABLE . '
				WHERE ' . $this->db->sql_in_set('post_id', array($postid));
			$result = $this->db->sql_query($sql);
			$sfs_done = (int) $this->db->sql_fetchfield('sfs_reported');
			$this->db->sql_freeresult($result);

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

				// Report the uhmmm reported?
				$this->check_report($postid);

				$sql = 'UPDATE ' . POSTS_TABLE . '
					SET sfs_reported = 1
					WHERE post_id = ' . (int) $postid;
				$this->db->sql_query($sql);

				$sfs_username = $this->user->lang('SFS_USERNAME_STOPPED', $username);

				$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_REPORTED', false, array($sfs_username, 'forum_id' => $this->forumid, 'topic_id' => $this->topicid, 'post_id'  => $postid));

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

	private function check_report($postid)
	{
		$sql = 'SELECT t.*, p.*
			FROM ' . POSTS_TABLE . ' p, ' . TOPICS_TABLE . ' t
			WHERE p.post_id = ' . (int) $postid . '
				AND p.topic_id = t.topic_id';
		$result = $this->db->sql_query($sql);
		$report_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$report_data)
		{
			throw new http_exception(403, 'POST_NOT_EXIST');
		}

		$this->forumid						= (int) $report_data['forum_id'];
		$this->topicid						= (int) $report_data['topic_id'];

		$reported_post_text					= $report_data['post_text'];
		$reported_post_bitfield				= $report_data['bbcode_bitfield'];
		$reported_post_uid					= $report_data['bbcode_uid'];
		$reported_post_enable_bbcode		= $report_data['enable_bbcode'];
		$reported_post_enable_smilies		= $report_data['enable_smilies'];
		$reported_post_enable_magic_url		= $report_data['enable_magic_url'];

		$sql = 'SELECT *
			FROM ' . FORUMS_TABLE . '
			WHERE forum_id = ' . (int) $this->forumid;
		$result = $this->db->sql_query($sql);
		$forum_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$forum_data)
		{
			throw new http_exception(403, 'FORUM_NOT_EXIST');
		}

		// if the post isn't reported, then report it
		if (!$report_data['post_reported'] && $this->config['sfs_notify'])
		{
			$report_name = 'other';
			$report_text = $this->user->lang('SFS_WAS_REPORTED');

			$sql = 'SELECT *
				FROM ' . REPORTS_REASONS_TABLE . "
				WHERE reason_title = '" . $this->db->sql_escape($report_name). "'";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);

			if ($row['reason_id'])
			{
				$sql_ary = array(
					'reason_id'							=> (int) $row['reason_id'],
					'post_id'							=> (int) $postid,
					'pm_id'								=> 0,
					'user_id'							=> (int) $this->user->data['user_id'],
					'user_notify'						=> 0,
					'report_closed'						=> 0,
					'report_time'						=> (int) time(),
					'report_text'						=> $report_text,
					'reported_post_text'				=> $reported_post_text,
					'reported_post_uid'					=> $reported_post_uid,
					'reported_post_bitfield'			=> $reported_post_bitfield,
					'reported_post_enable_bbcode'		=> $reported_post_enable_bbcode,
					'reported_post_enable_smilies'		=> $reported_post_enable_smilies,
					'reported_post_enable_magic_url'	=> $reported_post_enable_magic_url,
				);

				$sql = 'INSERT INTO ' . REPORTS_TABLE . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
				$this->db->sql_query($sql);

				$sql = 'UPDATE ' . POSTS_TABLE . '
					SET post_reported = 1
					WHERE post_id = ' . (int) $postid;
				$this->db->sql_query($sql);

				if (!$report_data['topic_reported'])
				{
					$sql = 'UPDATE ' . TOPICS_TABLE . '
						SET topic_reported = 1
						WHERE topic_id = ' . (int) $this->topicid . '
							OR topic_moved_id = ' . (int) $this->topicid;
					$this->db->sql_query($sql);
				}

				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->add_notifications('notification.type.report_post', array_merge($report_data, $row, $forum_data, array(
					'report_text'	=> $report_text,
				)));
			}
		}
	}
}
