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

class version_120 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return ['\phpbbmodders\stopforumspam\migrations\version_104'];
	}

	public function update_schema()
	{
		return [
			'add_columns'	=> [
				$this->table_prefix . 'privmsgs'        => [
					'sfs_reported'	=> ['BOOL', 0],
				],
			],
		];
	}
	public function revert_schema()
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'privmsgs'	=> [
					'sfs_reported',
				],
			],
		];
	}
}
