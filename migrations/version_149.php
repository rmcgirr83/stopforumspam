<?php
/**
 *
 * Stop Forum Spam extension for the phpBB Forum Software package
 *
 * @copyright (c) Stop Forum Spam
 * @author 2026 Rich McGirr (RMcGirr83)
 * @copyright (c) 2026, phpBB Modders, https://www.phpbbmodders.com/
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbmodders\stopforumspam\migrations;

/**
* Migration to add fake redirect spammers option
*/

class version_149 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\phpbbmodders\stopforumspam\migrations\version_122'];
	}

	public function update_data()
	{
		return [
			['config.add', ['sfs_fake_redirect_spammers', 0]],
		];
	}
}
