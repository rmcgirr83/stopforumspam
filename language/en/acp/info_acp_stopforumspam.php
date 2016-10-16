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
	'ACP_SFS_TITLE'			=> 'Stop Forum Spam',
	'SFS_CONTROL'			=> 'Stop Forum Spam Settings',
	'SFS_SETTINGS'			=> 'Settings',
	'SFS_ENABLED'			=> 'Enable Stop Forum Spam',
	'SFS_ENABLED_EXPLAIN'	=> 'Enable or disable the extension.  This applies to both user registration and guest posts.',
	'SFS_THRESHOLD_SCORE'	=> 'Stop Forum Spam threshold',
	'SFS_THRESHOLD_SCORE_EXPLAIN'	=> 'The extension will check against a threshold (e.g., the number of times a user name, email or IP address is found within the stop forum database). You can input any number between 1 and 99.  The lower the number the greater the possibility of a false positive.',
	'SFS_LOG_MESSAGE'			=> 'Log a message',
	'SFS_LOG_MESSAGE_EXPLAIN'	=> 'If set as yes messages will be logged in the ACP in either the admin or user logs stating the action done.',
	'SFS_BAN_IP'			=> 'Ban IP',
	'SFS_BAN_IP_EXPLAIN'	=> 'If set as yes the users IP will be banned for one hour',
	'SFS_BAN_REASON'		=> 'Display reason if banned',
	'SFS_BAN_REASON_EXPLAIN'	=> 'If “Ban IP” is set to yes, you can choose to display a message to the banned user or not.',
	'SFS_DOWN'				=> 'Allow if Stop Forum Spam is down',
	'SFS_DOWN_EXPLAIN'		=> 'Should registration/posting go through if the stop forum spam website is down',
	'SFS_API_KEY'			=> 'Stop Forum Spam API key',
	'SFS_API_KEY_EXPLAIN'	=> 'If you want to submit spammers to the Stop Forum Spam database, input your API key from <a target="_new" href="http://www.stopforumspam.com/keys">stop forum spam</a> here.  You must be registered on the SFS website to get an API key',
	// ACP message logs
	'LOG_SFS_MESSAGE'			=> '<strong>Stop Forum Spam triggered</strong>:<br />Username: %1$s<br />IP: %2$s<br />Email: %3$s',
	'LOG_SFS_DOWN'			=> '<strong>Stop Forum Spam was down during a registration or a forum post</strong>',
	'LOG_SFS_DOWN_USER_ALLOWED' => '<strong>Stop Forum Spam was down.</strong> Following user was allowed on the forum:<br />Username: %1$s<br />IP:%2$s<br />Email: %3$s',
	'LOG_SFS_NEED_CURL'		=> 'The stop forum spam extension needs <strong>cURL</strong> to work correctly.  Please speak to your server host to get cURL installed and active.',
	'LOG_SFS_CONFIG_SAVED'	=> '<strong>Stop Forum Spam settings changed</strong>',
	'SFS_BY_NAME'	=> 'Check against user name',
	'SFS_BY_EMAIL'	=> 'Check against email',
	'SFS_BY_IP'		=> 'Check against IP',
	'TOO_SMALL_SFS_THRESHOLD'	=> 'The threshold value is too small.',
	'TOO_LARGE_SFS_THRESHOLD'	=> 'The threshold value is too large.',
	'SFS_SETTINGS_ERROR'		=> 'There was an error saving your settings. Please submit the back trace with your error report.',
	'SFS_SETTINGS_SUCCESS'		=> 'The settings were successfully saved.',
));
