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

namespace phpbbmodders\stopforumspam\acp;

class stopforumspam_info
{
	public function module()
	{
		return [
			'filename'	=> '\phpbbmodders\stopforumspam\acp\stopforumspam_module',
			'title'	=> 'SFS_CONTROL',
			'version'	=> '1.1.0',
			'modes'	=> [
				'settings'	=> ['title' => 'SFS_CONTROL', 'auth' => 'ext_phpbbmodders/stopforumspam && acl_a_board', 'cat' => ['SFS_CONTROL']],
			],
		];
	}
}
