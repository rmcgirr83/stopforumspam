<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
* Swedish translation by Holger (http://www.maskinisten.net)
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
	'NO_SOUP_FOR_YOU'	=> 'Stop! Det ser ut som om du har flaggats som spammare hos Stop Forum Spam.<br />Om du tror att detta är felaktigt bör du kontakta %sforumets adminstratör%s.',
	'NO_SOUP_FOR_YOU_NO_CONTACT'	=> 'Stop! Det ser ut som om du har flaggats som spammare hos Stop Forum Spam.',
	'SFS_IP_STOPPED'	=> '<a target="_new" title="Kolla upp IP hos StopForumSpam.com (öppnas i ett nytt fönster)" href="http://www.stopforumspam.com/ipcheck/%1$s">%1$s</a>',
	'SFS_USERNAME_STOPPED'	=> '<a target="_new" title="Kolla upp medlemsnamnet hos StopForumSpam.com (öppnas i ett nytt fönster)" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_EMAIL_STOPPED'	=> '<a target="_new" title="Kolla upp e-postadressen hos StopForumSpam.com (öppnas i ett nytt fönster)" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_ERROR_MESSAGE'	=> 'Tyvärr kan vi inte utföra din begäran på grund av ett problem hos en tredje parts tjänst. Försök igen senare.',
	'SFS_POSTING'	=> 'Ingen e-post, försökte skriva ett inlägg',
	'SFS_BANNED'	=> 'Hittades i databasen hos Stop Forum Spam',
));
