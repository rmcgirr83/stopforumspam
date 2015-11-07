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
	static public function depends_on()
	{
		return array('\rmcgirr83\stopforumspam\migrations\version_102');
	}

	public function update_data()
	{
		$this->config_text = new \phpbb\config\db_text($this->db, $this->table_prefix . 'config_text');

		$settings = $this->config_text->get('sfs_settings');
		$settings = unserialize($settings);
		$this->config_text->delete('sfs_settings');
		return(array(
			array('config.add', array('allow_sfs', $settings['allow_sfs'])),
			array('config.add', array('sfs_threshold', $settings['sfs_threshold'])),
			array('config.add', array('sfs_ban_ip', $settings['sfs_ban_ip'])),
			array('config.add', array('sfs_log_message', $settings['sfs_log_message'])),
			array('config.add', array('sfs_down', $settings['sfs_down'])),
			array('config.add', array('sfs_by_name', $settings['sfs_by_name'])),
			array('config.add', array('sfs_by_email', $settings['sfs_by_email'])),
			array('config.add', array('sfs_by_ip', $settings['sfs_by_ip'])),
			array('config.add', array('sfs_ban_reason', $settings['sfs_ban_reason'])),
			array('config.remove', array('sfs_version')),
		));
	}
}
