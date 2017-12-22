<?php

/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace rmcgirr83\stopforumspam\event;

/**
* Event listener
*/
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	private $sfs_admins_mods = array();

	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/* @var \rmcgirr83\stopforumspam\core\sfsgroups */
	protected $sfsgroups;

	/* @var \rmcgirr83\stopforumspam\core\sfsapi */
	protected $sfsapi;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string phpEx */
	protected $php_ext;

	/* @var \rmcgirr83\contactadmin\controller\main_controller */
	protected $contactadmin;

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\controller\helper $helper,
		\phpbb\log\log $log,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\rmcgirr83\stopforumspam\core\sfsgroups $sfsgroups,
		\rmcgirr83\stopforumspam\core\sfsapi $sfsapi,
		$root_path,
		$php_ext,
		\rmcgirr83\contactadmin\controller\main_controller $contactadmin = null)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->user = $user;
		$this->helper = $helper;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->sfsgroups = $sfsgroups;
		$this->sfsapi = $sfsapi;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->contactadmin = $contactadmin;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.user_setup'						=> 'user_setup',
			'core.ucp_register_data_after'			=> 'user_sfs_validate_registration',
			'core.posting_modify_template_vars'		=> 'poster_data_email',
			'core.posting_modify_message_text'		=> 'poster_modify_message_text',
			'core.posting_modify_submission_errors'	=> 'user_sfs_validate_posting',
			// report to sfs?
			'core.viewtopic_before_f_read_check'	=> 'viewtopic_before_f_read_check',
			'core.viewtopic_post_rowset_data'		=> 'viewtopic_post_rowset_data',
			'core.viewtopic_modify_post_row'		=> 'viewtopic_modify_post_row',
			// Custom events for integration with Contact Admin Extension
			'rmcgirr83.contactadmin.modify_data_and_error'	=> 'user_sfs_validate_registration',
		);
	}

	public function user_setup($event)
	{
		//Need to load lang vars for mcp logs
		if ($this->user->page['page_name'] == 'mcp' . $this->php_ext)
		{
			$this->user->add_lang_ext('rmcgirr83/stopforumspam', 'sfs_mcp');
		}
	}

	public function user_sfs_validate_registration($event)
	{
		if ($this->config['allow_sfs'] == false)
		{
			return;
		}
		$this->user->add_lang_ext('rmcgirr83/stopforumspam', 'stopforumspam');

		$error_array = $event['error'];

		/* On registration and only when all errors have cleared
		 * do not want the admin message area to fill up
		 * stopforumspam only works with IPv4 not IPv6
		*/
		if (!sizeof($error_array))
		{
			$check = $this->stopforumspam_check($event['data']['username'], $this->user->ip, $event['data']['email']);

			if ($check)
			{
				if ($this->config['sfs_down'] && is_string($check))
				{
					return;
				}
				$error_array[] = $this->show_message($check);
				// now ban the spammer by IP
				if ($this->config['sfs_ban_ip'] && !is_string($check))
				{
					$this->ban_by_ip($this->user->ip);
				}
			}
		}
		$event['error'] = $error_array;
	}

	/*
	* inject email for anonymous postings
	* it is strictly used as a check against SFS
	*/
	public function poster_data_email($event)
	{
		if ($this->user->data['user_id'] == ANONYMOUS && $this->config['allow_sfs'])
		{
			// Output the data vars to the template
			$this->template->assign_vars(array(
					'SFS'	=> true,
					'EMAIL'	=> $this->request->variable('email', ''),
			));
		}
	}

	public function poster_modify_message_text($event)
	{
		$event['post_data'] = array_merge($event['post_data'], array(
			'email'	=> strtolower($this->request->variable('email', '')),
		));
	}

	public function user_sfs_validate_posting($event)
	{
		$error_array = $event['error'];

		if ($this->user->data['user_id'] == ANONYMOUS && $this->config['allow_sfs'])
		{
			$this->user->add_lang_ext('rmcgirr83/stopforumspam', 'stopforumspam');
			$this->user->add_lang('ucp');
			if (!function_exists('phpbb_validate_email'))
			{
				include($this->root_path . 'includes/functions_user.' . $this->php_ext);
			}
			// ensure email is populated on posting
			$error = $this->validate_email($event['post_data']['email']);
			if ($error)
			{
				$error_array[] = $this->user->lang[$error . '_EMAIL'];
			}
			// I just hate empty usernames for guest posting
			if (empty($event['post_data']['username']))
			{
				$username_error = $this->validate_username($event['post_data']['username']);
				if ($username_error)
				{
					$error_array[] = $username_error;
				}
			}

			if (!sizeof($error_array))
			{
				$check = $this->stopforumspam_check($event['post_data']['username'], $this->user->ip, $event['post_data']['email']);

				if ($check)
				{
					if ($this->config['sfs_down'] && is_string($check))
					{
						return;
					}
					$error_array[] = $this->show_message($check);

					// now ban the spammer by IP
					if ($this->config['sfs_ban_ip'] && !is_string($check))
					{
						$this->ban_by_ip($this->user->ip);
					}
				}
			}
		}
		$event['error'] = $error_array;
	}

	/*
	 * viewtopic_before_f_read_check() 	inject lang vars and grab admins and mods
	 * @param 		$event				\phpbb\event
	 * @return		null
	*/
	public function viewtopic_before_f_read_check($event)
	{
		if ($this->config['allow_sfs'])
		{
			$this->user->add_lang_ext('rmcgirr83/stopforumspam', 'stopforumspam');

			// get mods and admins
			$this->sfs_admins_mods = $this->sfsgroups->getadminsmods($event['forum_id']);
		}
	}

	/*
	 * viewtopic_post_rowset_data	add the posters ip into the rowset
	 * @param	$event				\phpbb\event
	 * @return	string
	*/
	public function viewtopic_post_rowset_data($event)
	{
		$rowset = $event['rowset_data'];
		$row = $event['row'];

		$rowset['poster_ip'] = $row['poster_ip'];
		$rowset['user_email'] = $row['user_email'];
		$rowset['sfs_reported'] = $row['sfs_reported'];

		$event['rowset_data'] = $rowset;
	}

	/*
	 * viewtopic_modify_post_row		show a link to admins and mods to report the spammer
	 * @param 	$event					\phpbb\event
	 * @return	string
	*/
	public function viewtopic_modify_post_row($event)
	{
		if (empty($this->config['allow_sfs']) || empty($this->config['sfs_api_key']))
		{
			return;
		}
		$row = $event['row'];

		// ensure we have an IP and email address..this may happen if users have "post" bots on the forum
		$sfs_report_allowed = (!empty($row['poster_ip']) && !empty($row['user_email']) && $event['poster_id'] != ANONYMOUS) ? true : false;
		if ($sfs_report_allowed && in_array($this->user->data['user_id'], $this->sfs_admins_mods) && !in_array((int) $event['poster_id'], $this->sfs_admins_mods))
		{
			$reporttosfs_url = $this->helper->route('rmcgirr83_stopforumspam_core_reporttosfs', array('username' => urlencode($row['username']), 'userip' => $row['poster_ip'], 'useremail' => $row['user_email'], 'postid' => (int) $row['post_id'], 'posterid' => (int) $event['poster_id'], 'forumid' => $event['topic_data']['forum_id']));

			$report_link = phpbb_version_compare(PHPBB_VERSION, '3.2', '>=') ? '<a href="' . $reporttosfs_url . '" title="' . $this->user->lang['REPORT_TO_SFS']. '" data-ajax="reporttosfs" class="button button-icon-only"><i class="icon fa-exchange fa-fw" aria-hidden="true"></i><span>' . $this->user->lang['REPORT_TO_SFS'] . '</span></a>' : '<a href="' . $reporttosfs_url . '" title="' . $this->user->lang['REPORT_TO_SFS']. '" data-ajax="reporttosfs" class="button icon-button"><span>' . $this->user->lang['REPORT_TO_SFS'] . '</span></a>';

			$event['post_row'] = array_merge($event['post_row'], array(
				'SFS_LINK'			=> (!$row['sfs_reported']) ? $report_link : '',
			));
		}
	}

	/*
	 * show_message
	 * @param 	$check 	the type of check we are, uhmmm, checking
	 * @return string
	*/
	private function show_message($check = '')
	{
		if ($check === 'sfs_down')
		{
			return $this->user->lang['SFS_ERROR_MESSAGE'];
		}
		else
		{
			if ($this->contactadmin !== null && !empty($this->config['contactadmin_enable']))
			{
				$message = $this->user->lang('NO_SOUP_FOR_YOU', '<a href="' . $this->helper->route('rmcgirr83_contactadmin_displayform') . '">', '</a>');
			}
			else if ($this->config['contact_admin_form_enable'])
			{
				$link = ($this->config['email_enable']) ? append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=contactadmin') : 'mailto:' . htmlspecialchars($this->config['board_contact']);
				$message = $this->user->lang('NO_SOUP_FOR_YOU', '<a href="'. $link .'">','</a>');
			}
			else
			{
				$message = $this->user->lang('NO_SOUP_FOR_YOU_NO_CONTACT');
			}
			return $message;
		}
	}

	/*
	 * stopforumspam_check
	 * @param 	$username 	username from the forum inputs
	 * @param	$ip			the users ip
	 * @param	$email		email from the forum inputs
	 * @return 	bool		true if found, false if not
	*/
	private function stopforumspam_check($username, $ip, $email)
	{
		// Default value
		$spam_score = 0;

		$sfs_log_message = !empty($this->config['sfs_log_message']) ? $this->config['sfs_log_message'] : false;

		// Threshold score to reject registration and/or guest posting
		$sfs_threshold = !empty($this->config['sfs_threshold']) ? $this->config['sfs_threshold'] : 1;

		// Query the SFS database and pull the data into script
		$json = $this->sfsapi->sfsapi('query', $username, $ip, $email, $this->config['sfs_api_key']);
		$json_decode = json_decode($json, true);

		// Check if user is a spammer, but only if we successfully got the SFS data
		if ($json_decode['success'])
		{
			$username_freq = $json_decode['username']['frequency'];
			$email_freq = $json_decode['email']['frequency'];
			$ip_freq = $json_decode['ip']['frequency'];

			// ACP settings in effect
			if ($this->config['sfs_by_name'] == false)
			{
				$username_freq = 0;
			}

			if ($this->config['sfs_by_email'] == false)
			{
				$email_freq = 0;
			}

			if ($this->config['sfs_by_ip'] == false)
			{
				$ip_freq = 0;
			}
			// Return the total score
			$spam_score = ($username_freq + $email_freq + $ip_freq);

			// If we've got a spammer we'll take away their soup!
			if ($spam_score >= $sfs_threshold)
			{
				if ($sfs_log_message)
				{
					$this->log_message('user', $username, $ip, 'LOG_SFS_MESSAGE', $email);
				}
				//user is a spammer
				return true;
			}
			else
			{
				return false;
			}
		}
		else
		{
			if ($sfs_log_message)
			{
				if ($this->config['sfs_down'])
				{
					$this->log_message('admin', $username, $ip, 'LOG_SFS_DOWN_USER_ALLOWED', $email);
				}
				else
				{
					$this->log_message('admin', $username, $ip, 'LOG_SFS_DOWN', $email);
				}
			}
			return 'sfs_down';
		}
	}

	// log messages
	private function log_message($mode, $username, $ip, $message, $email)
	{
		$sfs_ip_check = $this->user->lang('SFS_IP_STOPPED', $ip);
		$sfs_username_check = $this->user->lang('SFS_USERNAME_STOPPED', $username);
		$sfs_email_check = $this->user->lang('SFS_EMAIL_STOPPED', $email);

		if ($mode === 'admin')
		{
			$this->log->add('admin', $this->user->data['user_id'], $ip, $message, false, array($sfs_username_check, $sfs_ip_check, $sfs_email_check));
		}
		else
		{
			$this->log->add('user', $this->user->data['user_id'], $ip, $message, false, array('reportee_id' => $this->user->data['user_id'], $sfs_username_check, $sfs_ip_check, $sfs_email_check));
		}
	}

	// validate email on posting
	private function validate_email($email)
	{
		$error = phpbb_validate_email($email);

		return $error;
	}

	// validate username on posting
	private function validate_username($username)
	{
		$error = array();
		if (($result = validate_username($username)) !== false)
		{
			$error[] = $this->user->lang[$result . '_USERNAME'];
		}

		if (($result = validate_string($username, false, $this->config['min_name_chars'], $this->config['max_name_chars'])) !== false)
		{
			$min_max_amount = ($result == 'TOO_SHORT') ? $this->config['min_name_chars'] : $this->config['max_name_chars'];
			$error[] = $this->user->lang('FIELD_' . $result, $min_max_amount, $this->user->lang['USERNAME']);
		}

		return $error;
	}

	// ban a nub
	private function ban_by_ip($ip)
	{
		$ban_reason = (!empty($this->config['sfs_ban_reason'])) ? $this->user->lang['SFS_BANNED'] : '';
		// ban the nub
		user_ban('ip', $ip, (int) $this->config['sfs_ban_time'], 0, false, $this->user->lang['SFS_BANNED'], $ban_reason);

		return;
	}
}
