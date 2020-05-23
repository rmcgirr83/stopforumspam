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
		global $cache, $config, $db, $request, $template, $user;
		global $phpbb_container, $phpbb_root_path, $phpEx, $phpbb_log;

		$user->add_lang_ext('rmcgirr83/stopforumspam', 'acp/acp_stopforumspam');
		$user->add_lang('acp/ban');

		$sfsgroups = $phpbb_container->get('rmcgirr83.stopforumspam.core.sfsgroups');

		$action = $request->variable('action', '');

		$this->page_title = $user->lang['SFS_CONTROL'];
		$this->tpl_name = 'stopforumspam_body';

		add_form_key('sfs');
		$curl_active = $this->allow_sfs();

		$cache_built = '';
		if (!$cache->get('_sfs_adminsmods') && $config['sfs_api_key'])
		{
			$cache_built = $user->lang('SFS_NEED_CACHE');
		}

		switch ($action)
		{
			case 'clr_reports':

				$sql = 'UPDATE ' . POSTS_TABLE . ' SET sfs_reported = 0
					WHERE sfs_reported = 1';
				$db->sql_query($sql);

				$sql = 'UPDATE ' . PRIVMSGS_TABLE . ' SET sfs_reported = 0
					WHERE sfs_reported = 1';
				$db->sql_query($sql);

				$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_SFS_REPORTED_CLEARED');

				if ($request->is_ajax())
				{

					$data = [
						'REFRESH_DATA'			=> [
							'url'	=> '',
							'time'	=> 5,
						],
						'MESSAGE_TITLE'	=> $user->lang('SUCCESS'),
						'MESSAGE_TEXT'	=> $user->lang('SFS_REPORTED_CLEARED'),
					];

					$json_response = new \phpbb\json_response;
					$json_response->send($data);
				}

			break;

			case 'build_adminsmods':

				if (empty($config['sfs_api_key']))
				{
					trigger_error('SFS_NEEDS_API');
				}

				$sfsgroups->build_adminsmods_cache();

				$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_ADMINSMODS_CACHE_BUILT');

				if ($request->is_ajax())
				{
					$data = [
						'REFRESH_DATA'			=> [
							'url'	=> '',
							'time'	=> 5,
						],
						'MESSAGE_TITLE'	=> $user->lang('SUCCESS'),
						'MESSAGE_TEXT'	=> $user->lang('LOG_ADMINSMODS_CACHE_BUILT'),

					];
					$json_response = new \phpbb\json_response;
					$json_response->send($data);
				}
			break;
		}

		if ($request->is_set_post('submit'))
		{
			// Test if the submitted form is valid
			if (!check_form_key('sfs'))
			{
				trigger_error($this->user->lang['FORM_INVALID'] . adm_back_link($this->u_action));
			}

			if (!function_exists('validate_data'))
			{
				include($phpbb_root_path . 'includes/functions_user.' . $phpEx);
			}

			$has_api_key = $request->variable('sfs_api_key', '', true);

			$check_row = array('sfs_threshold' => $request->variable('sfs_threshold', 0));
			$validate_row = array('sfs_threshold' => array('num', false, 1, 99));
			$error = validate_data($check_row, $validate_row);

			// Replace "error" strings with their real, localised form
			$error = array_map(array($user, 'lang'), $error);

			if (!sizeof($error))
			{
				if (!empty($has_api_key))
				{
					$sfsgroups->build_adminsmods_cache();
				}

				// Set the options the user configured
				$this->set_options();

				$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_SFS_CONFIG_SAVED');

				trigger_error($user->lang['SFS_SETTINGS_SUCCESS'] . adm_back_link($this->u_action));
			}
		}

		$url = $this->u_action;

		$sql = 'SELECT COUNT(sfs_reported) AS stat
			FROM ' . POSTS_TABLE . '
			WHERE sfs_reported = 1';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchfield('stat');
		$db->sql_freeresult($result);

		$posts_reported = $row;

		$sql = 'SELECT COUNT(sfs_reported) AS stat
			FROM ' . PRIVMSGS_TABLE . '
			WHERE sfs_reported = 1';
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchfield('stat');
		$db->sql_freeresult($result);

		$pms_reported = $row;

		$template->assign_vars(array(
			'ERROR'			=> isset($error) ? ((sizeof($error)) ? implode('<br />', $error) : '') : '',
			'SFS_API_KEY'	=> $config['sfs_api_key'],
			'ALLOW_SFS'		=> ($config['allow_sfs'] && $curl_active) ? true : false,
			'CURL_ACTIVE'	=> (!$curl_active) ? $user->lang['LOG_SFS_NEED_CURL'] : false,
			'SFS_THRESHOLD'	=> (int) $config['sfs_threshold'],
			'SFS_BAN_IP'	=> ($config['sfs_ban_ip']) ? true : false,
			'SFS_LOG_MESSAGE'	=> ($config['sfs_log_message']) ? true : false,
			'SFS_DOWN'		=> ($config['sfs_down']) ? true : false,
			'SFS_BY_NAME'	=> ($config['sfs_by_name']) ? true : false,
			'SFS_BY_EMAIL'	=> ($config['sfs_by_email']) ? true : false,
			'SFS_BY_IP'		=> ($config['sfs_by_ip']) ? true : false,
			'SFS_BAN_REASON'	=> ($config['sfs_ban_reason']) ? true : false,
			'SFS_BAN_TIME'	=> $this->display_ban_time($config['sfs_ban_time']),
			'SFS_NOTIFY'	=> ($config['sfs_notify']) ? true : false,
			'NOTICE'	=> $cache_built,
			'L_SFS_CLEAR_EXPLAIN'	=> $user->lang('SFS_CLEAR_EXPLAIN', (int) $posts_reported, (int) $pms_reported),

			'U_BUILD_CACHE'	=> $url . '&amp;action=build_adminsmods',
			'U_CLR_REPORTS'	=> $url . '&amp;action=clr_reports',
			'U_ACTION'		=> $url,
		));
	}

	protected function allow_sfs()
	{
		global $config;

		// Determine if cURL is enabled on the server
		$curl = false;
		if (function_exists('curl_init'))
		{
			$curl = true;
		}

		if (!$curl)
		{
			$config->set('allow_sfs', false);
		}

		return $curl;
	}

	/**
	 * Set the options a user can configure
	 *
	 * @return void
	 * @access protected
	 */
	protected function set_options()
	{
		global $config, $request;

		$config->set('sfs_threshold', $request->variable('sfs_threshold', 0));
		$config->set('allow_sfs', $request->variable('allow_sfs', 0));
		$config->set('sfs_ban_ip', $request->variable('sfs_ban_ip', 0));
		$config->set('sfs_log_message', $request->variable('sfs_log_message', 0));
		$config->set('sfs_down', $request->variable('sfs_down', 0));
		$config->set('sfs_by_name', $request->variable('sfs_by_name', 0));
		$config->set('sfs_by_email', $request->variable('sfs_by_email', 0));
		$config->set('sfs_by_ip', $request->variable('sfs_by_ip', 0));
		$config->set('sfs_ban_reason', $request->variable('sfs_ban_reason', 0));
		$config->set('sfs_api_key', $request->variable('sfs_api_key', '', true));
		$config->set('sfs_ban_time', $request->variable('sfs_ban_time', 0));
		$config->set('sfs_notify', $request->variable('sfs_notify', 0));
	}

	protected function display_ban_time($ban_time = 0)
	{
		global $user;

		// Ban length options
		$ban_text = array(0 => $user->lang['PERMANENT'], 30 => $user->lang['30_MINS'], 60 => $user->lang['1_HOUR'], 360 => $user->lang['6_HOURS'], 1440 => $user->lang['1_DAY'], 10080 => $user->lang['7_DAYS'], 20160 => $user->lang['2_WEEKS'], 40320 => $user->lang['1_MONTH'], 524160 => $user->lang['1_YEAR']);

		$ban_options = '';
		foreach ($ban_text as $length => $text)
		{
			$selected = ($length == $ban_time) ? ' selected="selected"' : '';
			$ban_options .= "<option value='{$length}'$selected>$text</option>";
		}

		return $ban_options;
	}
}
