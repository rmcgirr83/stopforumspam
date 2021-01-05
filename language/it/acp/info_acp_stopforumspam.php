<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2021 Marco (marcomg)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/
if (!defined('IN_PHPBB'))
{
	exit;
}
if (empty($lang) || !is_array($lang))
{
	$lang = [];
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


$lang = array_merge($lang, [

	// ACP entries
	'ACP_SFS_TITLE'			=> 'Stop Forum Spam',
	'SFS_CONTROL'			=> 'Impostazioni Stop Forum Spam',
	// ACP message logs
	'LOG_SFS_MESSAGE'		=> '<strong>Stop Forum Spam attivato</strong>:<br />Username: %1$s<br />IP: %2$s<br />Email: %3$s',
	'LOG_SFS_DOWN'			=> '<strong>Stop Forum Spam era offline durante la registrazione o l’invio di un post al forum</strong>',
	'LOG_SFS_DOWN_USER_ALLOWED' => '<strong>Stop Forum Spam era offline.</strong> Il seguetne utente è stato autorizzato sul forum:<br />Username: %1$s<br />IP:%2$s<br />Email: %3$s',
	'LOG_SFS_NEED_CURL'		=> 'L’estensione stop forum spam necessita di <strong>cURL</strong> per funzionare correttamente. Per cortesia contatta il tuo host per avere cURL installato e attivo.',
	'LOG_SFS_CONFIG_SAVED'	=> '<strong>Impostazioni di Stop Forum Spam aggiornate correttamente</strong>',
	'LOG_SFS_REPORTED'		=> '<strong>L’utente è stato segnalato a Stop Forum Spam</strong><br>» %1$s',
	'LOG_SFS_PM_REPORTED'	=> '<strong>I PM degli utenti sono stati segnalati a Stop Forum Spam</strong><br>» %1$s',
	'LOG_SFS_REPORTED_CLEARED'	=> 'I post e i messaggi privati segnalati a stop forum spam sono stati cancellati',
	'LOG_ADMINSMODS_CACHE_BUILT'	=> 'La cache degli admin e dei moderatori per Stop forum spam è stata prodotta',
]);
