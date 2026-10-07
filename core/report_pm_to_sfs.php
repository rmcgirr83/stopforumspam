<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) 2020 Rich McGirr (RMcGirr83)
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\core;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface as db;
use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\log\log;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\user;
use phpbbmodders\stopforumspam\core\sfsgroups;
use phpbbmodders\stopforumspam\core\sfsapi;
use Symfony\Component\DependencyInjection\ContainerInterface;

use phpbb\exception\http_exception;
use phpbb\report\exception\invalid_report_exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
* Reports the author of a private message to Stop Forum Spam
*/
class report_pm_to_sfs
{
	/** @var auth $auth */
	protected $auth;

	/** @var config $config */
	protected $config;

	/** @var db $db */
	protected $db;

	/** @var helper $helper */
	protected $helper;

	/** @var language $language */
	protected $language;

	/** @var log $log */
	protected $log;

	/** @var request $request */
	protected $request;

	/** @var template $template */
	protected $template;

	/** @var user $user */
	protected $user;

	/** @var sfsgroups $sfsgroups */
	protected $sfsgroups;

	/** @var sfsapi $sfsapi */
	protected $sfsapi;

	/** @var ContainerInterface */
	protected $container;

	public function __construct(
			auth $auth,
			config $config,
			db $db,
			helper $helper,
			language $language,
			log $log,
			request $request,
			template $template,
			user $user,
			sfsgroups $sfsgroups,
			sfsapi $sfsapi,
			ContainerInterface $container)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->language = $language;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->sfsgroups = $sfsgroups;
		$this->sfsapi = $sfsapi;
		$this->container = $container;
	}

	/**
	* report_pm_to_sfs
	* @param	int		$postid			the pm msgid
	* @param	int		$posterid		the author id of the pm
	* @return 	json response
	*/
	public function report_pm_to_sfs($postid, $posterid)
	{
		$postid = (int) $postid;
		$posterid = (int) $posterid;

		$this->language->add_lang('stopforumspam', 'phpbbmodders/stopforumspam');
		if (!$this->user->data['is_registered'] || !$this->config['allow_privmsg'] || !$this->auth->acl_get('u_readpm') ||
			$this->user->data['user_id'] == $posterid)
		{
			throw new http_exception(403, 'SFS_PM_REPORT_NOT_ALLOWED');
		}

		$admins_mods = $this->sfsgroups->getadminsmods(0);

		// Check if reporting PMs is enabled
		if (!$this->config['allow_pm_report'] || in_array($posterid, $admins_mods) || empty($this->config['sfs_report_pm']))
		{
			throw new http_exception(403, 'SFS_PM_REPORT_NOT_ALLOWED');
		}

		if (empty($this->config['allow_sfs']) || empty($this->config['sfs_api_key']))
		{
			throw new http_exception(403, 'SFS_MISSING_DATA');
		}

		// postid must be greater than 0
		if ($postid <= 0)
		{
			throw new http_exception(403, 'SFS_PM_NOT_EXIST');
		}

		$sql = $this->db->sql_build_query('SELECT', [
			'SELECT'	=> 'pm.sfs_reported, pm.author_id, pm.author_ip, u.username, u.user_email',
			'FROM'		=> [
				PRIVMSGS_TABLE		=> 'pm',
				PRIVMSGS_TO_TABLE	=> 'pt',
			],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [USERS_TABLE => 'u'],
					'ON'	=> 'u.user_id = pm.author_id',
				],
			],
			'WHERE'		=> 'pm.msg_id = ' . (int) $postid . '
				AND pm.author_id = ' . (int) $posterid . '
				AND pt.msg_id = pm.msg_id
				AND pt.user_id = ' . (int) $this->user->data['user_id'] . '
				AND pt.pm_deleted = 0
				AND ' . $this->db->sql_in_set('pt.folder_id', [PRIVMSGS_NO_BOX, PRIVMSGS_HOLD_BOX], true),
		]);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// info must exist
		if (!$row)
		{
			throw new http_exception(403, 'SFS_INFO_NOT_FOUND');
		}

		$username = $row['username'];
		$userip = $row['author_ip'];
		$useremail = $row['user_email'];
		$sfs_reported = (int) $row['sfs_reported'];

		// ensure the IP is something other than 127.0.0.1 which can happen if the anonymised extension is installed
		if (filter_var($userip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_RES_RANGE) === false)
		{
			throw new http_exception(403, 'SFS_ANONYMIZED_IP');
		}

		if ($sfs_reported)
		{
			throw new http_exception(403, 'SFS_PM_REPORTED');
		}

		// fix confirm box non-ajax error (controller must return)
		if ($this->request->is_set_post('cancel') && !$this->request->is_ajax())
		{
			return $this->helper->message('SFS_OPERATION_CANCELED');
		}

		if (confirm_box(true))
		{
			$response = $this->sfsapi->sfsapi('add', $username, $userip, $useremail, '', $this->config['sfs_api_key']);

			// An add request returns true only on success; cURL errors return a non-empty string.
			if ($response !== true && $this->request->is_ajax())
			{
				$data = [
					'MESSAGE_TITLE'	=> $this->language->lang('ERROR'),
					'MESSAGE_TEXT'	=> $this->language->lang('SFS_ERROR_MESSAGE'),
					'success'	=> false,
				];
				return new JsonResponse($data);
			}
			else if ($response !== true)
			{
				$this->template->assign_vars([
					'MESSAGE_TITLE' => $this->language->lang('ERROR'),
					'MESSAGE_TEXT'	=> $this->language->lang('SFS_ERROR_MESSAGE')
				]);

				return $this->helper->render('message_body.html');
			}

			// Report the uhmmm reported?
			if ($this->config['sfs_notify'])
			{
				$this->check_report($postid);
			}

			$sql = 'UPDATE ' . PRIVMSGS_TABLE . '
				SET sfs_reported = 1
				WHERE msg_id = ' . (int) $postid;
			$this->db->sql_query($sql);

			$sfs_username = $this->language->lang('SFS_USERNAME_STOPPED', $username);

			$this->log->add('mod', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_PM_REPORTED', false, [$sfs_username, 'msg_id'  => $postid]);

			if ($this->request->is_ajax())
			{
				$data = [
					'MESSAGE_TITLE'	=> $this->language->lang('SFS_SUCCESS'),
					'MESSAGE_TEXT'	=> $this->language->lang('SFS_SUCCESS_MESSAGE'),
					'success'	=> true,
					'postid'	=> $postid,
				];

				return new JsonResponse($data);
			}
			else
			{
				$this->template->assign_vars([
					'MESSAGE_TITLE' => $this->language->lang('SFS_SUCCESS'),
					'MESSAGE_TEXT'	=> $this->language->lang('SFS_SUCCESS_MESSAGE')
				]);

				return $this->helper->render('message_body.html');
			}
		}
		else
		{
			if ($this->request->is_ajax())
			{
				confirm_box(
					false,
					$this->language->lang('SFS_CONFIRM'),
					'',
					'confirm_body.html',
					$this->helper->route(
						'phpbbmodders_stopforumspam_core_report_pm_to_sfs',
						[
							'postid' => $postid,
							'posterid' => $posterid,
						],
						true,
						false,
						UrlGeneratorInterface::ABSOLUTE_URL
					)
				);
			}
			else
			{
				confirm_box(false, $this->language->lang('SFS_CONFIRM'));
			}
		}
	}

	/**
	* check_report			add a board report for the PM, unless it is already reported
	*
	* Failures are ignored: the PM has already been reported to Stop Forum Spam
	* by the time this runs, so a missing report reason or a report the board refuses
	* must not stop the PM being marked.
	*
	* @param 	int	$msg_id 	msg_id from the report to sfs
	* @return 	void
	*/
	private function check_report($msg_id)
	{
		$sql = 'SELECT reason_id
			FROM ' . REPORTS_REASONS_TABLE . "
			WHERE reason_title = 'other'";
		$result = $this->db->sql_query($sql);
		$reason_id = (int) $this->db->sql_fetchfield('reason_id');
		$this->db->sql_freeresult($result);

		if (!$reason_id)
		{
			return;
		}

		try
		{
			$report_handler = $this->container->get('phpbb.report.handlers.report_handler_pm');
			$report_handler->add_report($msg_id, $reason_id, $this->language->lang('SFS_PM_WAS_REPORTED'), 0);
		}
		catch (invalid_report_exception $e)
		{
			// Already reported, or the board does not allow this user to report it
		}
	}
}
