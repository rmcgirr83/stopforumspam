<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters for use
// ’ » “ ” …


$lang = array_merge($lang, array(

	// ACP entries
	'SFS_SETTINGS'			=> 'Settings',
	'SFS_ENABLED'			=> 'Enable Stop Forum Spam',
	'SFS_ENABLED_EXPLAIN'	=> 'Enable or disable the extension.  This applies to both user registration and guest posts.',
	'SFS_THRESHOLD_SCORE'	=> 'Stop Forum Spam threshold',
	'SFS_THRESHOLD_SCORE_EXPLAIN'	=> 'The extension will check against a threshold (e.g., the number of times a user name, email or IP address is found within the stop forum database). You can input any number between 1 and 99.  The lower the number the greater the possibility of a false positive.',
	'SFS_LOG_MESSAGE'		=> 'Log a message',
	'SFS_LOG_MESSAGE_EXPLAIN'	=> 'If set as yes messages will be logged in the ACP in either the admin or user logs stating the action done.',
	'SFS_BAN_IP'			=> 'Ban IP',
	'SFS_BAN_IP_EXPLAIN'	=> 'If set as yes the users IP will be banned per the setting of “Length of ban”',
	'SFS_BAN_REASON'		=> 'Display reason if banned',
	'SFS_BAN_REASON_EXPLAIN'	=> 'If “Ban IP” is set to yes, you can choose to display a message to the banned user or not.',
	'SFS_DOWN'				=> 'Allow if Stop Forum Spam is down',
	'SFS_DOWN_EXPLAIN'		=> 'Should registration/posting go through if the stop forum spam website is down',
	'SFS_API_KEY'			=> 'Stop Forum Spam API key',
	'SFS_API_KEY_EXPLAIN'	=> 'If you want to submit spammers to the Stop Forum Spam database, input your API key from <a target="_new" href="http://www.stopforumspam.com/keys">stop forum spam</a> here.  You must be registered on the SFS website to get an API key',
	'SFS_NOTIFY'			=> 'Board Notification',
	'SFS_NOTIFY_EXPLAIN'	=> 'If set yes and there is an API key set above, then board notifications will also be triggered when a post is reported to stop forum spam',
	'SFS_CLEAR'	=> 'Reset reported posts',
	'SFS_CLEAR_EXPLAIN'	=> 'Will reset all posts reported to stop forum spam',
	// ACP messages
	'SFS_BY_NAME'	=> 'Check against user name',
	'SFS_BY_EMAIL'	=> 'Check against email',
	'SFS_BY_IP'		=> 'Check against IP',
	'TOO_SMALL_SFS_THRESHOLD'	=> 'The threshold value is too small.',
	'TOO_LARGE_SFS_THRESHOLD'	=> 'The threshold value is too large.',
	'SFS_SETTINGS_ERROR'		=> 'There was an error saving your settings. Please submit the back trace with your error report.',
	'SFS_SETTINGS_SUCCESS'		=> 'The settings were successfully saved.',
	'SFS_REPORTED_CLEARED' => 'Posts reported to stop forum spam were reset.',
));
