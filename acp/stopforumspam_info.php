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

namespace rmcgirr83\stopforumspam\acp;

class stopforumspam_info
{
	function module()
	{
		return array(
			'filename'	=> '\rmcgirr83\stopforumspam\acp\stopforumspam_module',
			'title'	=> 'SFS_CONTROL',
			'version'	=> '1.1.0',
			'modes'	=> array(
				'settings'	=> array('title' => 'SFS_CONTROL', 'auth' => 'ext_rmcgirr83/stopforumspam && acl_a_board', 'cat' => array('SFS_CONTROL')),
			),
		);
	}
}
