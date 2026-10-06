<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) Stop Forum Spam
 * @author 2022 Rich McGirr (RMcGirr83)
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\migrations;

/**
* Primary migration
*/

class version_122 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\phpbbmodders\stopforumspam\migrations\version_121'];
	}

	public function update_data()
	{
		return [
			['config.add', ['sfs_contactadmin', 1]],
			['config.update', ['sfs_log_message', 1]],
		];
	}
}
