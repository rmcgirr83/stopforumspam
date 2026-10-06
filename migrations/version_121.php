<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) Stop Forum Spam
 * @author 2017 Rich McGirr (RMcGirr83)
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\migrations;

/**
* Primary migration
*/

class version_121 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\phpbbmodders\stopforumspam\migrations\version_120'];
	}

	public function update_data()
	{
		return [
			['config.add', ['sfs_report_pm', 0]],
		];
	}
}
