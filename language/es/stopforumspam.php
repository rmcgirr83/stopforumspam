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
	'NO_SOUP_FOR_YOU'	=> '¡No hay sopa para usted! Parece que se le ha señalado como un spammer.<br />Si cree que esta decisión fue tomada por error %scontacte con el Administrador del foro%s.',
	'NO_SOUP_FOR_YOU_NO_CONTACT'	=> '¡No hay sopa para usted! Parece que se le ha señalado como un spammer.',
	'SFS_IP_STOPPED'	=> '<a target="_new" title="Comprobar IP en StopForumSpam.com (se abre en una nueva ventana)" href="http://www.stopforumspam.com/ipcheck/%1$s">%1$s</a>',
	'SFS_USERNAME_STOPPED'	=> '<a target="_new" title="Comprobar nombre de usuario en StopForumSpam.com (se abre en una nueva ventana)" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_EMAIL_STOPPED'	=> '<a target="_new" title="Comprobar correo electrónico en StopForumSpam.com (se abre en una nueva ventana)" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_ERROR_MESSAGE'	=> 'Desafortunadamente, no podemos procesar su solicitud ahora debido a problemas con una parte externa. Puede intentarlo de nuevo más tarde.',
	'SFS_POSTING'	=> 'Sin correo electrónico, ha intentado incluir un mensaje',
));
