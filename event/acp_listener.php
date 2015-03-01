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
		if ($event['mode'] == 'registration')
		{
			// Store display_vars event in a local variable
			$display_vars = $event['display_vars'];

			// Define config vars
			$config_vars = array(
				'allow_sfs' 	=> array('lang' => 'SFS_ENABLED', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sfs_threshold' => array('lang' => 'SFS_THRESHOLD_SCORE', 'validate' => 'int:1:99', 'type' => 'number:1:99', 'explain' => true),
				'sfs_down'		=> array('lang' => 'SFS_DOWN', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sfs_log_message' => array('lang' => 'SFS_LOG_MESSAGE', 'validate' => 'bool', 'type' => 'radio:yes_no', 'explain' => true),
				'sfs_api_key' 	=> array('lang' => 'SFS_API_KEY', 'validate' => 'string:0:14', 'type' => 'text:14:14', 'explain' => true),
			);

			$display_vars['vars'] = phpbb_insert_config_array($display_vars['vars'], $config_vars, array('after' =>'chg_passforce'));

			// Update the display_vars  event with the new array
			$event['display_vars'] = array('title' => $display_vars['title'], 'vars' => $display_vars['vars']);
		}
	}
}
