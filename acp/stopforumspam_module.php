<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) Stop Forum Spam
* @author 2015 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace rmcgirr83\stopforumspam\acp;

class stopforumspam_module
{
	public	$u_action;

	function main($id, $mode)
	{
		global $db, $config, $request, $template, $user;
		global $phpbb_root_path, $phpbb_admin_path, $phpEx;

		$this->page_title = $user->lang['SFS_CONTROL'];
		$this->tpl_name = 'stopforumspam_body';

		add_form_key('sfs');
		$allow_sfs = $this->allow_sfs();
		// Get saved settings.
		$sql = 'SELECT * FROM ' . CONFIG_TEXT_TABLE . "
				WHERE config_name = 'sfs_settings'";
		$result = $db->sql_query($sql);
		$settings = $db->sql_fetchfield('config_value');
		$db->sql_freeresult($result);

		if (!empty($settings))
		{
			$settings = unserialize($settings);
		}
		else
		{
			// Default settings in case something went wrong with the install.
			$settings = array(
				'allow_sfs'		=> $allow_sfs,
				'sfs_threshold'	=> 5,
				'sfs_ban_ip'	=> 0,
				'sfs_log_message'	=> 0,
				'sfs_down'		=> 0,
				'sfs_by_name'	=> 1,
				'sfs_by_email'	=> 1,
				'sfs_by_ip'		=> 1,
				'sfs_ban_reason'	=> 1,
			);
		}

		if ($request->is_set_post('submit'))
		{
			// Test if form key is valid
			if (!check_form_key('sfs'))
			{
				trigger_error($user->lang['FORM_INVALID'] . adm_back_link($this->u_action), E_USER_WARNING);
			}
			if (!function_exists('validate_data'))
			{
				include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			}

			$check_row = array('sfs_threshold' => $request->variable('sfs_threshold', 0));
			$validate_row = array('sfs_threshold' => array('num', false, 1, 99));
			$error = validate_data($check_row, $validate_row);

			// Replace "error" strings with their real, localised form
			$error = array_map(array($user, 'lang'), $error);

			if (!sizeof($error))
			{
				$settings = array(
					'allow_sfs'		=> !empty($allow_sfs) ? $request->variable('allow_sfs', 0) : false,
					'sfs_threshold'		=> $request->variable('sfs_threshold', 0),
					'sfs_ban_ip'	=> $request->variable('sfs_ban_ip', 0),
					'sfs_log_message'	=> $request->variable('sfs_log_message', 0),
					'sfs_down'		=> $request->variable('sfs_down', 0),
					'sfs_by_name'	=> $request->variable('sfs_by_name', 0),
					'sfs_by_email'	=> $request->variable('sfs_by_email', 0),
					'sfs_by_ip'		=> $request->variable('sfs_by_ip', 0),
					'sfs_ban_reason'	=> $request->variable('sfs_ban_reason', 0),
				);

				$sql_settings	= serialize($settings);
				$sql_settings	= $db->sql_escape($sql_settings);

				$sql = 'UPDATE ' . CONFIG_TEXT_TABLE . "
						SET config_value = '$sql_settings'
						WHERE config_name = 'sfs_settings'";
				$success = $db->sql_query($sql);

				if ($success === false)
				{
					trigger_error($user->lang['SFS_SETTINGS_ERROR'] . adm_back_link($this->u_action), E_USER_ERROR);
				}
				else
				{
					trigger_error($user->lang['SFS_SETTINGS_SUCCESS'] . adm_back_link($this->u_action));
				}
			}
		}

		$template->assign_vars(array(
			'ERROR'			=> isset($error) ? ((sizeof($error)) ? implode('<br />', $error) : '') : '',
			'ALLOW_SFS'		=> (!empty($settings['allow_sfs'])) ? true : false,
			'CURL_ACTIVE'	=> (!empty($allow_sfs)) ? '' : '<br /><span class="error">' . $user->lang['LOG_SFS_NEED_CURL'] .'</span>',
			'SFS_THRESHOLD'	=> (!empty($settings['sfs_threshold'])) ? $settings['sfs_threshold'] : 1,
			'SFS_BAN_IP'	=> (!empty($settings['sfs_ban_ip'])) ? true : false,
			'SFS_LOG_MESSAGE'	=> (!empty($settings['sfs_log_message'])) ? true : false,
			'SFS_DOWN'		=> (!empty($settings['sfs_down'])) ? true : false,
			'SFS_BY_NAME'	=> (!empty($settings['sfs_by_name'])) ? true : false,
			'SFS_BY_EMAIL'	=> (!empty($settings['sfs_by_email'])) ? true : false,
			'SFS_BY_IP'		=> (!empty($settings['sfs_by_ip'])) ? true : false,
			'SFS_BAN_REASON'	=> (!empty($settings['sfs_ban_reason'])) ? true : false,
			'SFS_VERSION'		=> $config['sfs_version'],

			'U_ACTION'			=> $this->u_action,
		));
	}

	function allow_sfs()
	{
		// Determine if cURL is enabled on the server
		$curl = false;
		if (function_exists('curl_init'))
		{
			$curl = true;
		}

		return $curl;
	}
}
