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

	/** @var \phpbb\log\log */
	protected $log;

	/** @var string phpBB root path */
	protected $phpbb_root_path;

	/** @var string phpEx */
	protected $php_ext;
	/**
	* Constructor
	* NOTE: The parameters of this method must match in order and type with
	* the dependencies defined in the services.yml file for this service.
	*
	* @param \phpbb\config	$config		Config object
	* @param \phpbb\user		$user		User object
	* @param \phpbb\controller\helper		$helper				Controller helper object
	*/
	public function __construct(\phpbb\config\config $config, \phpbb\user $user, \phpbb\log\log $log, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->user = $user;
		$this->log = $log;
		$this->root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	static public function getSubscribedEvents()
	{
		return array(
			'core.ucp_register_data_after'			=> 'user_sfs_validate',
			'core.posting_modify_submission_errors'	=> 'user_sfs_validate',
		);
	}

	public function user_sfs_validate($event)
	{
		if (empty($this->config['allow_sfs']))
		{
			return;
		}

		$this->user->add_lang_ext('rmcgirr83/stopforumspam', 'stopforumspam');

		$array = $event['error'];

		/* On registration and only when all errors have cleared
		 * do not want the admin message area to fill up
		*/
		if (!empty($event['data']['username']) && !empty($event['data']['email']) && !sizeof($array))
		{
			$check = $this->stopforumspam_check($event['data']['username'], $this->user->ip, $event['data']['email']);

			if ($check)
			{
				if ($this->config['sfs_down'] && $check === 'sfs_down')
				{
					return;
				}
				$array[] = $this->get_message($check);
			}
		}
		/* when posting..there is no $event['data'], it is $event['post_data']
		 * only check when all errors have cleared and only for guests
		 * do not want the admin message area to fill up
		*/
		else if (!empty($event['post_data']['username']) && $this->user->data['user_id'] == ANONYMOUS && !sizeof($array))
		{
			$check = $this->stopforumspam_check($event['post_data']['username'], $this->user->ip, false);

			if ($check)
			{
				if ($this->config['sfs_down'] && $check === 'sfs_down')
				{
					return;
				}
				$array[] = $this->get_message($check);
			}
		}
		$event['error'] = $array;
	}

	/*
	 * get_message
	 * @param 	$check 	the type of check we are, uhmmm, checking
	 * @return string
	*/
	private function get_message($check)
	{
		if ($check === 'sfs_down')
		{
			return $this->user->lang['SFS_ERROR_MESSAGE'];
		}
		else
		{
			return $this->user->lang('NO_SOUP_FOR_YOU', '<a href="' . append_sid("{$this->root_path}memberlist.$this->php_ext", 'mode=contactadmin') . '">', '</a>');
		}
	}

	/*
	 * stopforumspam_check
	 * @param 	$username 	username from the forum inputs
	 * @param	$email		email from the forum inputs
	 * @param	$ip			the users ip
	 * @return 	bool		true if found, false if not
	*/
	private function stopforumspam_check($username, $ip, $email = false)
	{
		// we need to urlencode for spaces
		$username = urlencode($username);

		// Default value
		$spam_score = 0;
		$sfs_api_key = !empty($this->config['sfs_api_key']) ? $this->config['sfs_api_key'] : false;

		$sfs_log_message = !empty($this->config['sfs_log_message']) ? $this->config['sfs_log_message'] : false;

		/* Threshold score to reject registration and/or guest posting */
		$sfs_threshold = !empty($this->config['sfs_threshold']) ? $this->config['sfs_threshold'] : 0;

		// Query the SFS database and pull the data into script
		// email is only used during registration..posting username and ip
		if ($email)
		{
			$xmlUrl = 'http://www.stopforumspam.com/api?username='.$username.'&ip='.$ip.'&email='.$email.'&f=xmldom';
		}
		else
		{
			$xmlUrl = 'http://www.stopforumspam.com/api?username='.$username.'&ip='.$ip.'&f=xmldom';
		}
		$xmlStr = $this->get_file($xmlUrl);

		// Check if user is a spammer, but only if we successfully got the SFS data
		if ($xmlStr)
		{
			$xmlObj = simplexml_load_string($xmlStr);

			// Assign points for the total number of times each have been flagged
			$ck_username = $xmlObj->username->frequency;
			$ck_email = $xmlObj->email->frequency;
			$ck_ip = $xmlObj->ip->frequency;

			// Let's not ban a registrant with a common IP address, who is otherwise clean
			if ($ck_username + $ck_email == 0)
			{
				$ck_ip = 0;
			}

			// Return the total score
			$spam_score = ($ck_username + $ck_email + $ck_ip);

			// If we've got a spammer we'll take away their soup!
			if ($spam_score >= $sfs_threshold)
			{
				//deencode the stuffs
				$username = urldecode($username);

				if (!empty($sfs_api_key) && !empty($email))
				{
					// add the spammer to the SFS database
					$http_request = 'http://www.stopforumspam.com/add.php';
					$http_request .= '?username=' . $username;
					$http_request .= '&ip_addr=' . $ip;
					$http_request .= '&email=' . $email;
					$http_request .= '&api_key=' . $sfs_api_key;

					$response = $this->get_file($http_request);

					if ($response === false && $sfs_log_message)
					{
						$message = 'LOG_SFS_ERROR_MESSAGE_ADMIN_REG';
						$this->log_message('admin', $username, $ip, $message, $email);
					}
					else if ($sfs_log_message)
					{
						$message = 'LOG_SFS_SUBMITTED';
						$this->log_message('user', $username, $ip, $message, $email);
					}
				}
				else if ($sfs_log_message)
				{
					$message = 'LOG_SFS_MESSAGE';
					$this->log_message('user', $username, $ip, $message, $email);
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
	private function log_message($mode, $username, $ip, $message, $email = false)
	{
		$sfs_ip_check = sprintf($this->user->lang['SFS_IP_STOPPED'], $ip);
		$sfs_username_check = sprintf($this->user->lang['SFS_USERNAME_STOPPED'], $username);
		$sfs_email_check = !empty($email) ? sprintf($this->user->lang['SFS_EMAIL_STOPPED'], $email) : $this->user->lang['SFS_POSTING'];

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
			if($httpcode != 200)
			{
				return false;
			}

			return $contents;
		}
	}
}
