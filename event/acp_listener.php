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

class acp_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return array(
			'core.acp_board_config_edit_add'	=>	'add_options',
		);
	}

	public function add_options($event)
	{
		if ($event['mode'] == 'registration' && isset($event['display_vars']['vars']['legend2']))
		{
			// Store display_vars event in a local variable
			$display_vars = $event['display_vars'];

			// Define config vars
			$config_vars = array(
				'legend_sfs'	=> 'SFS_CONTROL',
				'allow_sfs' 	=> array('lang' => 'SFS_ENABLED', 'validate' => 'bool', 'type' => 'custom', 'function' => array($this, 'allow_sfs'), 'explain' => true),
				'sfs_threshold' => array('lang' => 'SFS_THRESHOLD_SCORE', 'validate' => 'int:1:99', 'type' => 'number:1:99', 'explain' => true),
				'sfs_ban_ip'	=> array('lang' => 'SFS_BAN_IP', 'valdate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sfs_down'		=> array('lang' => 'SFS_DOWN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sfs_log_message' => array('lang' => 'SFS_LOG_MESSAGE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
			);

			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $config_vars, array('before' => 'legend2'));

			// Update the display_vars  event with the new array
			$event['display_vars'] = array('title' => $display_vars['title'], 'vars' => $display_vars['vars']);
		}
	}

	public function allow_sfs($key, $value)
	{
		global $user, $config;

		$radio_ary = array(1 => 'YES', 0 => 'NO');
		// Determine if cURL is enabled on the server
		$curl = false;
		if (function_exists('curl_init'))
		{
			$curl = true;
		}
		// if false...display a message
		$message = ($curl === false) ? 'LOG_SFS_NEED_CURL' : false;

		return h_radio('config[allow_sfs]', $radio_ary, $key) .
		($message !== false ? '<br /><span class="error">' . $user->lang($message) . '</span>' : '');
	}
}
