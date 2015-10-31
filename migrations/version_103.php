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

class version_103 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['sfs_version']) && version_compare($this->config['sfs_version'], '1.0.3', '>=');
	}

	static public function depends_on()
	{
		return array('\rmcgirr83\stopforumspam\migrations\version_102');
	}

	public function update_data()
	{
		$settings = $this->config_text->get(sfs_settings);
		$settings_array = unserialize($settings);
		return(array(
			array('config.add', array('sfs_settings', $settings['sfs_settings'])),
			array('config.add', array('allow_sfs'), $settings['allow_sfs']),
			array('config.add', array('sfs_threshold'), $settings['sfs_threshold']),
			array('config.add', array('sfs_ban_ip'), $settings['sfs_ban_ip']),
			array('config.add', array('sfs_log_message'), $settings['sfs_log_message']),
			array('config.add', array('sfs_down'), $settings['sfs_down']),
			array('config.update', array('sfs_version', '1.0.3')),
		));
	}
}
