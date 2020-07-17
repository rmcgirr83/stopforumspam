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

/**
* ignore
*/
use phpbb\config\config;
use Symfony\Component\DependencyInjection\ContainerInterface;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\log\log;
use phpbb\request\request;
use phpbb\user;
use rmcgirr83\stopforumspam\core\sfsgroups;
use rmcgirr83\stopforumspam\core\sfsapi;
use phpbb\exception\http_exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class report_pm_to_sfs
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var ContainerInterface */
	protected $container;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

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
			config $config,
			ContainerInterface $container,
			driver_interface $db,
			language $language,
			log $log,
			request $request,
			user $user,
			sfsgroups $sfsgroups,
			sfsapi $sfsapi)
	{
		$this->config = $config;
		$this->container = $container;
		$this->db = $db;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->user = $user;
		$this->sfsgroups = $sfsgroups;
		$this->sfsapi = $sfsapi;
	}

	/*
	* report_pm_to_sfs
	* @param	int		$msgid			the pm msgid
	* @param	int		$authorid		the author id of the pm
	* @return 	json response
	*/
	public function report_pm_to_sfs($msgid, $authorid)
	{
		$this->language->add_lang('stopforumspam', 'rmcgirr83/stopforumspam');

		$admins_mods = $this->sfsgroups->getadminsmods(0);

		$author_id = (int) $authorid;
		$msg_id = (int) $msgid;

		// Check if reporting PMs is enabled
		if (!$this->config['allow_pm_report'] || in_array($author_id, $admins_mods))
		{
			throw new http_exception(403, 'SFS_PM_REPORT_NOT_ALLOWED');
		}

		if (empty($this->config['allow_sfs']) || empty($this->config['sfs_api_key']))
		{
			return false;
		}

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
			throw new http_exception(403, 'SFS_PM_REPORTED');
		}

		// only allow this via ajax calls
		//
		if ($this->request->is_ajax())
		{
			$response = $this->sfsapi->sfsapi('add', $username, $userip, $useremail, $this->config['sfs_api_key']);

			if (!$response)
			{
				$data = [
					'MESSAGE_TITLE'	=> $this->user->lang('ERROR'),
					'MESSAGE_TEXT'	=> $this->user->lang('SFS_ERROR_MESSAGE'),
					'success'	=> false,
				];
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

			$sfs_username = $this->language->lang('SFS_USERNAME_STOPPED', $username);

			$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_PM_REPORTED', false, [$sfs_username, 'msg_id'  => $msg_id]);

			$data = [
				'MESSAGE_TITLE'	=> $this->language->lang('SUCCESS'),
				'MESSAGE_TEXT'	=> $this->language->lang('SFS_SUCCESS_MESSAGE'),
				'success'	=> true,
				'msg_id'	=> $msg_id,
			];

			return new JsonResponse($data);
		}
	}

	/*
	 * check_report					check to see if the PM msg has already been reported
	 * @param 	int	$msg_id 		msg_id from the report to sfs
	 * @return 	json response if found
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
			$data = [
				'MESSAGE_TITLE'	=> $this->user->lang('ERROR'),
				'MESSAGE_TEXT'	=> $this->user->lang('PM_NOT_EXIST'),
				'success'	=> false,
			];
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
