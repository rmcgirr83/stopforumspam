<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) Stop Forum Spam
* @author 2026 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace rmcgirr83\stopforumspam\migrations;

/**
* Migration to add fake redirect spammers option
*/

class version_149 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\rmcgirr83\stopforumspam\migrations\version_122'];
	}

	public function update_data()
	{
		return [
			['config.add', ['sfs_fake_redirect_spammers', 0]],
		];
	}
}
