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
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\user */
	protected $user;

	/** @var phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;

	public function __construct(
		\phpbb\config\config $config,
		\phpbb\user $user,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\controller\helper $helper,
		\phpbb\log\log $log,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		$phpbb_root_path,
		$php_ext,
		\rmcgirr83\contactadmin\controller\main_controller $contactadmin = null)
	{
		$this->config = $config;
		$this->user = $user;
		$this->db = $db;
		$this->helper = $helper;
		$this->log = $log;
		$this->request = $request;
		$this->template = $template;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->contactadmin = $contactadmin;
		if (!function_exists('phpbb_validate_email'))
		{
			include($this->root_path . 'includes/functions_user.' . $this->php_ext);
		}
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.ucp_register_data_after'			=> 'user_sfs_validate_registration',
			'core.posting_modify_template_vars'		=> 'poster_data_email',
			'core.posting_modify_message_text'		=> 'poster_modify_message_text',
			'core.posting_modify_submission_errors'	=> 'user_sfs_validate_posting',
			// Custom events for integration with Contact Admin Extension
			'rmcgirr83.contactadmin.modify_data_and_error'	=> 'user_sfs_validate_registration',
		);
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
		if (!sizeof($error_array) && strpos($this->user->ip, ':') == false)
		{
			$check = $this->stopforumspam_check($event['data']['username'], $this->user->ip, $event['data']['email']);

			if ($check)
			{
				if ($this->config['sfs_down'] && $check === 'sfs_down')
				{
					return;
				}
				$error_array[] = $this->show_message($check);
				// now ban the spammer by IP
				if ($this->config['sfs_ban_ip'])
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
					$error_array = array_merge($username_error, $error_array);
				}
			}
			// stopforumspam only works with IPv4 not IPv6
			if (!sizeof($error_array) && strpos($this->user->ip, ':') == false)
			{
				$check = $this->stopforumspam_check($event['post_data']['username'], $this->user->ip, $event['post_data']['email']);

				if ($check)
				{
					if ($this->config['sfs_down'] && $check === 'sfs_down')
					{
						return;
					}
					$error_array[] = $this->show_message($check);

					// now ban the spammer by IP
					if ($this->config['sfs_ban_ip'])
					{
						$this->ban_by_ip($this->user->ip);
					}
				}
			}
		}
		$event['error'] = $error_array;
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
				$link = ($this->config['contact_admin_form_enable'] && $this->config['email_enable']) ? append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=contactadmin') : 'mailto:' . htmlspecialchars($this->config['board_contact']);
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
		// we need to urlencode for spaces
		$username = urlencode($username);

		// Default value
		$spam_score = 0;

		$sfs_log_message = !empty($this->config['sfs_log_message']) ? $this->config['sfs_log_message'] : false;

		// Threshold score to reject registration and/or guest posting
		$sfs_threshold = !empty($this->config['sfs_threshold']) ? $this->config['sfs_threshold'] : 1;

		// Query the SFS database and pull the data into script
		$xmlUrl = 'http://www.stopforumspam.com/api?username='.$username.'&ip='.$ip.'&email='.$email.'&f=xmldom';

		$xmlStr = $this->get_file($xmlUrl);

		// Check if user is a spammer, but only if we successfully got the SFS data
		if ($xmlStr)
		{
			$xmlObj = simplexml_load_string($xmlStr);

			// Assign points for the total number of times each have been flagged
			$ck_username = $xmlObj->username->frequency;
			$ck_email = $xmlObj->email->frequency;
			$ck_ip = $xmlObj->ip->frequency;

			// ACP settings in effect
			if ($this->config['sfs_by_name'] == false)
			{
				$ck_username = 0;
			}

			if ($this->config['sfs_by_email'] == false)
			{
				$ck_email = 0;
			}

			if ($this->config['sfs_by_ip'] == false)
			{
				$ck_ip = 0;
			}
			// Return the total score
			$spam_score = ($ck_username + $ck_email + $ck_ip);

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
			$this->log->add('admin', $this->user->data['user_id'], $ip, $message, time(), array($sfs_username_check, $sfs_ip_check, $sfs_email_check));
		}
		else
		{
			$this->log->add('user', $this->user->data['user_id'], $ip, $message, time(), array('reportee_id' => $this->user->data['user_id'], $sfs_username_check, $sfs_ip_check, $sfs_email_check));
		}
	}

	// use curl to get response from SFS
	private function get_file($url)
	{
		// We'll use curl..most servers have it installed as default
		if (function_exists('curl_init'))
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

			return $contents;
		}

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_SFS_NEED_CURL', time());

		return false;
	}

	// validate email on posting
	private function validate_email($email)
	{
		$error = array();

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
		// ban the nub for one hour
		user_ban('ip', $ip, 60, 0, false, $this->user->lang['SFS_BANNED'], $ban_reason);

		return;
	}
}
