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

class stopforumspam_module
{
	/** @var string Form action URL, set by phpBB's module system */
	public $u_action;

	/** @var string Template file name */
	public $tpl_name;

	/** @var string Page title */
	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container;

		$this->tpl_name		= 'stopforumspam_body';
		$this->page_title	= $phpbb_container->get('language')->lang('SFS_CONTROL');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('phpbbmodders.stopforumspam.admin.controller');

		// Make the $u_action url available in the admin controller
		$admin_controller->set_page_url($this->u_action);

		$admin_controller->display_options();
	}
}
