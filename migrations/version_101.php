<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) Stop Forum Spam
 * @author 2015 Rich McGirr (RMcGirr83)
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\migrations;

/**
* Primary migration
*/

class version_101 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['sfs_version']) && version_compare($this->config['sfs_version'], '1.0.1', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbbmodders\stopforumspam\migrations\version_100');
	}

	public function update_data()
	{
		return array(
			array('config.remove', array('sfs_api_key')),
			array('config.update', array('sfs_version', '1.0.1')),
		);
	}

}
