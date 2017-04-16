<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
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

$lang = array_merge($lang, array(
	'NO_POST_SELECTED'	=> 'You must select at least one post to perform this action.',
	'NO_SOUP_FOR_YOU'	=> 'No soup for you! It looks like you have been flagged as a spammer.<br />If you feel this decision was made in error %scontact the board admin%s.',
	'NO_SOUP_FOR_YOU_NO_CONTACT'	=> 'No soup for you! It looks like you have been flagged as a spammer.',
	'SFS_IP_STOPPED'	=> '<a target="_new" title="Check IP at StopForumSpam.com (opens in a new window)" href="http://www.stopforumspam.com/ipcheck/%1$s">%1$s</a>',
	'SFS_USERNAME_STOPPED'	=> '<a target="_new" title="Check Username at StopForumSpam.com (opens in a new window)" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_EMAIL_STOPPED'	=> '<a target="_new" title="Check Email at StopForumSpam.com (opens in a new window)" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_ERROR_MESSAGE'	=> 'Unfortunately we can not process your request now due to problems with an external party. You can try again later.',
	'SFS_POSTING'	=> 'No email, tried making a post',
	'SFS_BANNED'	=> 'Found in the Stop Forum Spam database',
	'SFS_REPORTED'	=> 'Post has already been reported',
	'REPORT_TO_SFS'	=> 'Report to Stop Forum Spam',
	'SFS_SUCCESS_MESSAGE'	=> 'User was successfully reported to the stop forum database',
	'SFS_WAS_REPORTED'	=> 'Post was reported to Stop Forum Spam',
	'SFS_NEED_CURL'	=> 'The extension requires cURL which doesn’t seem to be installed',
	'LOG_SFS_REPORTED' => '<strong>User was reported to Stop Forum Spam</strong><br>» %1$s',
));
