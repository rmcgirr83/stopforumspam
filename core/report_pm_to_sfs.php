<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2020 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace rmcgirr83\stopforumspam\core;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbb\exception\http_exception;

class report_pm_to_sfs
{
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

	/* @var \rmcgirr83\stopforumspam\core\sfsapi */
	protected $sfsapi;

	public function __construct(
			\phpbb\config\config $config,
			ContainerInterface $container,
			\phpbb\db\driver\driver_interface $db,
			\phpbb\log\log $log,
			\phpbb\request\request $request,
			\phpbb\user $user,
			\rmcgirr83\stopforumspam\core\sfsgroups $sfsgroups,
			\rmcgirr83\stopforumspam\core\sfsapi $sfsapi)
	{
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->log = $log;
		$this->request = $request;
		$this->user = $user;
		$this->sfsgroups = $sfsgroups;
		$this->sfsapi = $sfsapi;
	}

	/*
	 * reporttosfs
	 * @param	$posterid	posterid that made the post
	 * @return 	json response
	*/
	public function report_pm_to_sfs($msgid, $authorid)
	{

		if (empty($this->config['allow_sfs']) || empty($this->config['sfs_api_key']))
		{
			return false;
		}

		$author_id = (int) $authorid;
		$msg_id = (int) $msgid;

		$this->user->add_lang_ext('rmcgirr83/stopforumspam', 'stopforumspam');

		// msg_id must be greater than 0
		if ($msg_id <= 0)
		{
			throw new http_exception(403, 'PM_NOT_EXIST');
		}

		$username = $userip = $sfs_reported = $useremail = '';

		$sql = 'SELECT pm.sfs_reported, pm.author_id, pm.author_ip, u.username, u.user_email
			FROM ' . PRIVMSGS_TABLE . ' pm
			LEFT JOIN ' . USERS_TABLE . ' u on pm.author_id = u.user_id
			WHERE pm.msg_id = ' . (int) $msg_id . ' AND pm.author_id = ' . (int) $author_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// info must exist
		if (!$row)
		{
			throw new http_exception(403, 'INFO_NOT_FOUND');
		}

		$username = $row['username'];
		$userip = $row['author_ip'];
		$useremail = $row['user_email'];
		$sfs_reported = (int) $row['sfs_reported'];

		if ($sfs_reported)
		{
			throw new http_exception(403, 'SFS_REPORTED');
		}

		$admins_mods = $this->sfsgroups->getadminsmods(0);

		// only allow this via ajax calls
		//
		if ($this->request->is_ajax() && !in_array($author_id, $admins_mods))
		{
			$response = $this->sfsapi->sfsapi('add', $username, $userip, $useremail, $this->config['sfs_api_key']);

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
			if ($this->config['sfs_notify'])
			{
				$this->check_report($msg_id);
			}

			$sql = 'UPDATE ' . PRIVMSGS_TABLE . '
				SET sfs_reported = 1
				WHERE msg_id = ' . (int) $msg_id;
			$this->db->sql_query($sql);

			$sfs_username = $this->user->lang('SFS_USERNAME_STOPPED', $username);

			$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_REPORTED', false, array($sfs_username, 'msg_id'  => $msg_id));

			$data = array(
				'MESSAGE_TITLE'	=> $this->user->lang('SUCCESS'),
				'MESSAGE_TEXT'	=> $this->user->lang('SFS_SUCCESS_MESSAGE'),
				'success'	=> true,
				'msg_id'	=> $msg_id,
			);
			return new JsonResponse($data);
		}
	}

	/*
	 * check_report
	 * @param 	$msg_id 	msg_id from the report to sfs
	 * @return 	null
	*/
	private function check_report($msg_id)
	{
		$sql = 'SELECT *
			FROM ' . PRIVMSGS_TABLE . '
			WHERE msg_id = ' . (int) $msg_id;
		$result = $this->db->sql_query($sql);
		$report_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$report_data)
		{
			$data = array(
				'MESSAGE_TITLE'	=> $this->user->lang('ERROR'),
				'MESSAGE_TEXT'	=> $this->user->lang('PM_NOT_EXIST'),
				'success'	=> false,
			);
			return new JsonResponse($data);
		}

		// if the pm isn't reported, then report it
		if (!$report_data['message_reported'])
		{
			$report_name = 'other';
			$report_text = $this->user->lang('SFS_PM_WAS_REPORTED');

			$sql = 'SELECT *
				FROM ' . REPORTS_REASONS_TABLE . "
				WHERE reason_title = '" . $this->db->sql_escape($report_name). "'";
			$result = $this->db->sql_query($sql);
			$row = $this->db->sql_fetchrow($result);
			$this->db->sql_freeresult($result);


			$phpbb_notifications = $this->container->get('phpbb.report.handlers.report_handler_pm');
			$phpbb_notifications->add_report($msg_id, $row['reason_id'], $report_text, 0);
		}
	}
}
