<?php
/**
*
* @package Stop Forum Spam
* @copyright (c) 2015 Rich McGirr(RMcGirr83)
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

function allow_sfs($key, $value)
{
	global $user, $config;

	$radio_ary = array(1 => 'YES', 0 => 'NO');
	
	// Determine if cURL is enabled on the server
	$curl = false;
	if (function_exists('curl_init'))
	{
		$curl = true;
	}

	// if false...turn the extension off
	if (!$curl)
	{
		$config->set('allow_sfs', 0);
	}
	$key = ($curl === false) ? 0 : $key;
	$message = ($curl === false) ? 'LOG_SFS_NEED_CURL' : false;

	// Let's do some friendly HTML injection if we want to disable the
	// form field because h_radio() has no pretty way of doing so
	$field_name = 'config[allow_sfs]' . ($message === 'LOG_SFS_NEED_CURL' ? '" disabled="disabled' : '');

	return h_radio($field_name, $radio_ary, $key) .
		($message !== false ? '<br /><span>' . $user->lang($message) . '</span>' : '');
}