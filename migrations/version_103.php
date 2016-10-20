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

namespace rmcgirr83\stopforumspam\migrations;

/**
* Primary migration
*/

class version_103 extends \phpbb\db\migration\container_aware_migration
{
	static public function depends_on()
	{
		return array('\rmcgirr83\stopforumspam\migrations\version_102');
	}

	public function update_data()
	{
		$config_text = $this->container->get('config_text');

		$settings = $config_text->get('sfs_settings');
		$settings = unserialize($settings);

		return array(
			array('config.add', array('allow_sfs', $this->get($settings['allow_sfs']))),
			array('config.add', array('sfs_threshold', $this->get($settings['sfs_threshold']))),
			array('config.add', array('sfs_ban_ip', $this->get($settings['sfs_ban_ip']))),
			array('config.add', array('sfs_log_message', $this->get($settings['sfs_log_message']))),
			array('config.add', array('sfs_down', $this->get($settings['sfs_down']))),
			array('config.add', array('sfs_by_name', $this->get($settings['sfs_by_name']))),
			array('config.add', array('sfs_by_email', $this->get($settings['sfs_by_email']))),
			array('config.add', array('sfs_by_ip', $this->get($settings['sfs_by_ip']))),
			array('config.add', array('sfs_ban_reason', $this->get($settings['sfs_ban_reason']))),
			array('config.remove', array('sfs_version')),
			array('config_text.remove', array('sfs_settings')),
		);
	}

	protected function get($setting)
	{
		return isset($setting) ? $setting : '';
	}
}
