<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\migrations;

/**
* Migration to add the option to show the board contact email to flagged users
*/

class version_161 extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['sfs_show_board_email']);
	}

	static public function depends_on()
	{
		return ['\phpbbmodders\stopforumspam\migrations\version_149'];
	}

	public function update_data()
	{
		// On by default, which keeps the behavior from before this option existed
		return [
			['config.add', ['sfs_show_board_email', 1]],
		];
	}
}
