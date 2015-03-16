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
	'SFS_CONTROL'			=> 'Stop Forum Spam',
	'SFS_ENABLED'			=> 'Habilitar Stop Forum Spam',
	'SFS_ENABLED_EXPLAIN'	=> 'Habilitar o Deshabilitar la extensión. Esto se aplica tanto el registro de usuarios y mensajes como invitado.',
	'SFS_THRESHOLD_SCORE'	=> 'Umbral de Stop Forum Spam',
	'SFS_THRESHOLD_SCORE_EXPLAIN'	=> 'La extensión comprobará con un umbral (por ejemplo, el número de veces que un nombre de usuario, correo electrónico o dirección IP se encuentra dentro de la base de datos de Stop Forum). Puede introducir cualquier número entre 1 y 99. Cuanto menor sea el número, mayor es la posibilidad de un falso positivo.',
	'SFS_LOG_MESSAGE'			=> 'Registrar un mensaje',
	'SFS_LOG_MESSAGE_EXPLAIN'	=> 'Si se define como sí, los mensajes se registran en el PCA cualquiera de los registros de Administrador o de usuario, y se indica la acción realizada.',
	'SFS_DOWN'				=> 'Permitir si Stop Forum Spam está caído',
	'SFS_DOWN_EXPLAIN'		=> 'En caso de que el registro/publicación pasan por si el sitio de Stop Forum Spam está caído',
	'SFS_API_KEY'			=> 'Clave API de Stop Forum Spam',
	'SFS_API_KEY_EXPLAIN'	=> 'Si desea enviar los spammers a la base de datos de Stop Forum Spam, introduzca su clave API de <a target="_new" href="http://www.stopforumspam.com/keys">Stop Forum Spam</a> aquí. Debe estar registrado en el sitio web de SFS para obtener su clave API',
	// ACP message logs
	'LOG_SFS_MESSAGE'			=> '<strong>Stop Forum Spam funcionando</strong>:<br />Nombre de usuario: %1$s<br />IP: %2$s<br />Email: %3$s',
	'LOG_SFS_ERROR_MESSAGE_ADMIN_REG'	=> '<strong>Stop Forum Spam no responde</strong><br />El registro de:<br />Nombre de usuario: %1$s<br />IP: %2$s<br />Email: %3$s<br />se detuvo, pero la extensión no pudo agregar el usuario a la base de datos de Stop Forum Spam',
	'LOG_SFS_SUBMITTED'		=> '<strong>Usuario añadido a la base de datos de Stop Forum Spam</strong>:<br />Nombre de usuario: %1$s<br />IP: %2$s<br />Email: %3$s',
	'LOG_SFS_DOWN'			=> '<strong>Stop Forum Spam estaba caído durante un registro o un mensaje en el foro</strong>',
	'LOG_SFS_DOWN_USER_ALLOWED' => '<strong>Stop Forum Spam está caído.</strong> Siguiendo usuario se le permitió en el foro:<br />Nombre de usuario: %1$s<br />IP:%2$s<br />Email: %3$s',
	'LOG_SFS_NEED_CURL'		=> 'La extensión de Stop Forum Spam necesita <strong>cURL</strong> para funcionar correctamente.  Por favor, hable con su hosting para obtener cURL instalado y activado.',
));
