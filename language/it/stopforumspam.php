<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2021 Marco (marcomg)
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
// Some characters you may want to copy&paste:
// ’ » “ ” …

$lang = array_merge($lang, [
	'CANNOT_REPORT_ANONYMOUS'	=> 'Non ti è permesso segnalare l’account anonimo.',
	'CANNOT_REPORT_ADMINS_MODS'	=> 'Non puoi segnalare gli amministratori o i moderatori di questo forum.',
	'FORUM_NOT_EXIST'		=> 'Il forum selezionato non esiste.',
	'INFO_NOT_FOUND'	=> 'La combinazione dell’id del post e l’del creatore non esiste.',
	'POST_NOT_EXIST'	=> 'Il post da te richiesto non esiste.',
	'NO_SOUP_FOR_YOU'	=> 'Non c’è trippa per gatti! Sei stato segnalato come spammer.<br />Se pensi che questo sia un errore %scontatta l’amministratore%s.',
	'NO_SOUP_FOR_YOU_NO_CONTACT'	=> 'Non c’è trippa per gatti! Sei stato segnalato come spammer.',
	'PM_NOT_EXIST'	=> 'Il PM non esiste',
	'SFS_ANONYMIZED_IP'	=> 'L’IP dell’utente è stato reso anonimo settandolo a 127.0.0.1, probabilmente ciò è dovuto ad un’estensione.',
	'SFS_IP_STOPPED'	=> '<a target="_blank" title="Controlla l’IP su StopForumSpam.com (nuova finestra)" href="http://www.stopforumspam.com/ipcheck/%1$s" rel="noreferrer noopener">%1$s</a>',
	'SFS_USERNAME_STOPPED'	=> '<a target="_blank" title="Controllo l’username su StopForumSpam.com (nuova finestra)" href="http://www.stopforumspam.com/search/?q=%1$s" rel="noreferrer noopener">%1$s</a>',
	'SFS_EMAIL_STOPPED'	=> '<a target="_blank" title="Controlla l’email su StopForumSpam.com (nuova finestra)" href="http://www.stopforumspam.com/search/?q=%1$s" rel="noreferrer noopener">%1$s</a>',
	'SFS_ERROR_MESSAGE'	=> 'Sfortunatamente non possiamo elaborare la tua richiesta ora a causa di problema esterno. Puoi riprovare più tardi.',
	'SFS_BANNED'	=> 'Trovato nel database di Stop Forum Spam',
	'SFS_USER_BANNED'	=> 'Bannato per un post sul forum',
	'SFS_REPORTED'		=> 'Il post è già stato segnalato',
	'SFS_PM_REPORTED'	=> 'Il PM è già stato segnalato',
	'REPORT_TO_SFS'	=> 'Segnala su Stop Forum Spam',
	'BUTTON_SFS'	=> 'Segnala su SFS',
	'SFS_SUCCESS_MESSAGE'	=> 'L’utente è stato segnalato sul database di SFS con successo',
	'SFS_WAS_REPORTED'	=> 'Il post è stato segnalato su Stop Forum Spam',
	'SFS_PM_WAS_REPORTED'	=> 'Il PM è stato segnalato a Stop Form Spam',
	'SFS_PM_REPORT_NOT_ALLOWED'	=> 'La segnalazione non è permessa',
	'SFS_NEED_CURL'	=> 'L’estensione richiede cURL che non sembra sia installato',
	'LOG_SFS_REPORTED' => '<strong>L’utente è stato segnalato su Stop Forum Spam</strong><br>» %1$s',
	'EXTENSION_REQUIREMENTS' => 'L’estensione richiede una versione di phpBB ≥ %1$s. Devi aggiornare la tua versione di phpBB per utilizzare questa estensione.',
]);
