<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) Stop Forum Spam
* @author 2017 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace rmcgirr83\stopforumspam\migrations;

/**
* Primary migration
*/

class version_120 extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\rmcgirr83\stopforumspam\migrations\version_104');
	}

	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'privmsgs'        => array(
					'sfs_reported'	=> array('BOOL', 0),
				),
			),
		);
	}
	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'privmsgs'	=> array(
					'sfs_reported',
				),
			),
		);
	}
}
