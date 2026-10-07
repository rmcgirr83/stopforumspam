<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) 2015 Rich McGirr (RMcGirr83)
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam;

/**
 * Stop Forum Spam extension base
 *
 * Handles the switch from the old "rmcgirr83/stopforumspam" package name: an existing
 * install's data is moved over to the new name when this extension is
 * enabled, so nothing is installed twice and no settings are lost.
 */
class ext extends \phpbb\extension\base
{
	/** Package name this extension used before the rename */
	const OLD_EXT_NAME = 'rmcgirr83/stopforumspam';

	/** Package name this extension uses now */
	const NEW_EXT_NAME = 'phpbbmodders/stopforumspam';

	/** PHP namespace prefix used before the rename */
	const OLD_NAMESPACE = '\\rmcgirr83\\stopforumspam\\';

	/** PHP namespace prefix used now */
	const NEW_NAMESPACE = '\\phpbbmodders\\stopforumspam\\';

	/**
	 * Refuse to enable below the minimum phpBB and PHP versions, or without
	 * cURL with SSL support, which every Stop Forum Spam request needs.
	 *
	 * Refuse to enable while the old copy is still enabled; both would run
	 * at once and the old one's data can't be moved while it's in use.
	 *
	 * @return bool|string|array True if enableable, otherwise a reason string or array of reasons
	 */
	public function is_enableable()
	{
		$language = $this->container->get('language');
		$language->add_lang('install_stopforumspam', 'phpbbmodders/stopforumspam');

		if (!$this->check_phpbb_version() || !$this->check_php_version())
		{
			return $language->lang('STOPFORUMSPAM_NOT_ENABLEABLE');
		}

		if (!$this->check_curl_ssl())
		{
			$language->add_lang('stopforumspam', 'phpbbmodders/stopforumspam');

			return $language->lang('CURL_REQUIREMENTS');
		}

		if ($this->container->get('ext.manager')->is_enabled(self::OLD_EXT_NAME))
		{
			return $language->lang('STOPFORUMSPAM_DISABLE_OLD', self::OLD_EXT_NAME);
		}

		return true;
	}

	/**
	 * Require phpBB 3.3.19
	 *
	 * @return bool
	 */
	public function check_phpbb_version()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.3.19', '>=');
	}

	/**
	 * Require PHP 7.4
	 *
	 * @return bool
	 */
	public function check_php_version()
	{
		return PHP_VERSION_ID >= 70400;
	}

	/**
	 * Require the cURL extension, built with SSL support
	 *
	 * @return bool
	 */
	public function check_curl_ssl()
	{
		if (!extension_loaded('curl'))
		{
			return false;
		}

		$curl_version = curl_version();

		return (bool) ($curl_version['features'] & CURL_VERSION_SSL);
	}

	/**
	 * Move an old install's data to the new name, then enable as usual.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return bool|string
	 */
	public function enable_step($old_state)
	{
		if ($old_state === false)
		{
			$this->move_old_install();

			// The migrator loaded its state before this ran; reload it so the
			// moved migration history counts as already installed.
			$this->migrator->load_migration_state();
		}

		return parent::enable_step($old_state);
	}

	/**
	 * Rewrite everything the database stores under the old name.
	 *
	 * Rows are filtered in PHP rather than with LIKE, because namespace
	 * backslashes and underscores are special characters in LIKE patterns
	 * on some databases. The tables involved are small.
	 */
	protected function move_old_install()
	{
		$db = $this->container->get('dbal.conn');
		$prefix = $this->container->getParameter('core.table_prefix');

		$db->sql_transaction('begin');

		// Migration history, including each migration's dependency list
		$result = $db->sql_query('SELECT migration_name, migration_depends_on FROM ' . $prefix . 'migrations');
		$rows = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			// Other extensions' migrations may depend on ours, so their
			// dependency lists are rewritten too, not just our own rows
			$old_depends_on = unserialize($row['migration_depends_on'], ['allowed_classes' => false]);
			$old_depends_on = is_array($old_depends_on) ? $old_depends_on : [];
			$depends_on = array_map([$this, 'new_class_name'], $old_depends_on);
			$name = $this->new_class_name($row['migration_name']);

			if ($name === $row['migration_name'] && $depends_on === $old_depends_on)
			{
				continue;
			}

			$db->sql_query('UPDATE ' . $prefix . 'migrations SET ' . $db->sql_build_array('UPDATE', [
				'migration_name'		=> $name,
				'migration_depends_on'	=> serialize($depends_on),
			]) . " WHERE migration_name = '" . $db->sql_escape($row['migration_name']) . "'");
		}

		// Module classes and their "ext_vendor/name" auth checks
		$result = $db->sql_query('SELECT module_id, module_basename, module_auth FROM ' . $prefix . 'modules');
		$rows = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		foreach ($rows as $row)
		{
			$basename = $this->new_class_name($row['module_basename']);
			$auth = str_replace('ext_' . self::OLD_EXT_NAME, 'ext_' . self::NEW_EXT_NAME, $row['module_auth']);

			if ($basename !== $row['module_basename'] || $auth !== $row['module_auth'])
			{
				$db->sql_query('UPDATE ' . $prefix . 'modules SET ' . $db->sql_build_array('UPDATE', [
					'module_basename'	=> $basename,
					'module_auth'		=> $auth,
				]) . ' WHERE module_id = ' . (int) $row['module_id']);
			}
		}

		// The old extension's own record (only once it's disabled)
		$db->sql_query('DELETE FROM ' . $prefix . "ext
			WHERE ext_name = '" . $db->sql_escape(self::OLD_EXT_NAME) . "'
				AND ext_active = 0");

		$db->sql_transaction('commit');
	}

	/**
	 * Map an old fully qualified class name to the new namespace.
	 *
	 * @param string $class_name Class name, possibly under the old namespace
	 * @return string
	 */
	protected function new_class_name($class_name)
	{
		if (!is_string($class_name))
		{
			return $class_name;
		}

		// Class names may be stored with or without the leading backslash
		foreach ([self::OLD_NAMESPACE, ltrim(self::OLD_NAMESPACE, '\\')] as $old)
		{
			if (strpos($class_name, $old) === 0)
			{
				$new = ($old === self::OLD_NAMESPACE) ? self::NEW_NAMESPACE : ltrim(self::NEW_NAMESPACE, '\\');
				return $new . substr($class_name, strlen($old));
			}
		}

		return $class_name;
	}
}
