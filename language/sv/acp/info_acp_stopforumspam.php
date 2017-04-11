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
	'SFS_CONTROL'			=> 'Stop Forum Spam',
	'SFS_ENABLED'			=> 'Aktivera Stop forum Spam',
	'SFS_ENABLED_EXPLAIN'	=> 'Aktiverar eller deaktiverar detta tillägg. Detta gäller både för registreringar och inlägg av gäster.',
	'SFS_THRESHOLD_SCORE'	=> 'Stop Forum Spam kvot',
	'SFS_THRESHOLD_SCORE_EXPLAIN'	=> 'Tillägget kommer att kontrollera med en kvot (dvs antalet gånger ett namn, en e-postadress eller IP-adress hittas i Stop Forum Spam databasen). Ange en siffra mellan 1 ochd 99. Ju lägre siffra, desto högre är risken att fellarm utlöses.',
	'SFS_LOG_MESSAGE'			=> 'Logga ett meddelande',
	'SFS_LOG_MESSAGE_EXPLAIN'	=> 'Ställ in till Ja för att logga händelser i antingen admin- eller användarloggen.',
	'SFS_BAN_IP'			=> 'Banna IP',
	'SFS_BAN_IP_EXPLAIN'	=> 'Ställ in till Ja för att banna användarens IP en timme.',
	'SFS_DOWN'				=> 'Tillåt undantag om Stop Forum Spam är nere',
	'SFS_DOWN_EXPLAIN'		=> 'Skall registreringar/inlägg gå igenom om Stop Forum Spam är nere.',
	'SFS_API_KEY'			=> 'Stop Forum Spam API-nyckel',
	'SFS_API_KEY_EXPLAIN'	=> 'Ange API-nyckeln om du vill registrera spammare i databasen hos Stop Forum Spam, en nyckel kan du få hos <a target="_new" href="http://www.stopforumspam.com/keys">Stop Forum Spam</a>. Du måste registrera dig hos SFS för att få en API-nyckel',
	// ACP message logs
	'LOG_SFS_MESSAGE'			=> '<strong>Stop Forum Spam utlöst</strong>:<br />Medlemsnamn: %1$s<br />IP: %2$s<br />E-post: %3$s',
	'LOG_SFS_ERROR_MESSAGE_ADMIN_REG'	=> '<strong>Stop Forum Spam inget svar</strong><br />Registreringen av:<br />Medlemsnamn: %1$s<br />IP: %2$s<br />E-post: %3$s<br />har stoppats men tillägget kunde ej lägga till användaren i databasen hos Stop Forum Spam database',
	'LOG_SFS_SUBMITTED'		=> '<strong>Användaren har lagts till i databasen hos Stop Forum Spam</strong>:<br />Medlemsnamn: %1$s<br />IP: %2$s<br />E-post: %3$s',
	'LOG_SFS_DOWN'			=> '<strong>Stop Forum Spam låg nere under registreringen eller inlägget</strong>',
	'LOG_SFS_DOWN_USER_ALLOWED' => '<strong>Stop Forum Spam låg nere.</strong> Följande användare tilläts undantagsvis i forumet:<br />Medlemsnamn: %1$s<br />IP:%2$s<br />E-post: %3$s',
	'LOG_SFS_NEED_CURL'		=> 'Tillägget Stop Forum Spam kräver <strong>cURL</strong> för att fungera korrekt. Installera och aktivera cURL på din server.',
));
