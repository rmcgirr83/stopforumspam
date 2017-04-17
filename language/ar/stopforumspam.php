<?php
/**
*
* Stop forum Spam extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 Rich McGirr (RMcGirr83)
* @license GNU General Public License, version 2 (GPL-2.0)
*
* Translated By : Bassel Taha Alhitary - www.alhitary.net
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'NO_SOUP_FOR_YOU'	=> 'لا تستطيع تنفيذ هذه الخطوة ! لأنه تم تسجيلك كأحد المُزعجين للمنتدى.<br />إذا رأيت أن هذا خطأ بواسطة البرنامج لدينا %sنرجوا الإتصال بمدير المنتدى%s.',
	'NO_SOUP_FOR_YOU_NO_CONTACT'	=> 'لا تستطيع تنفيذ هذه الخطوة ! لأنه تم تسجيلك كأحد المُزعجين للمنتدى.',
	'SFS_IP_STOPPED'	=> '<a target="_new" title="فحص رقم الآيبي IP في الموقع StopForumSpam.com ( فتح بنافذة جديدة )" href="http://www.stopforumspam.com/ipcheck/%1$s">%1$s</a>',
	'SFS_USERNAME_STOPPED'	=> '<a target="_new" title="فحص إسم المستخدم في الموقع StopForumSpam.com ( فتح بنافذة جديدة )" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_EMAIL_STOPPED'	=> '<a target="_new" title="فحص  البريد الإلكتروني في الموقع StopForumSpam.com ( فتح بنافذة جديدة )" href="http://www.stopforumspam.com/search/?q=%1$s">%1$s</a>',
	'SFS_ERROR_MESSAGE'	=> 'للأسف لا نستطيع مُعالجة طلبك الآن بسبب وجود مشاكل في سيرفر الطرف الخارجي. نرجوا المحاولة مرة أخرى لاحقاً.',
	'SFS_POSTING'	=> 'لا يوجد بريد الكتروني , حاول إضافة مُشاركة',
	'SFS_BANNED'	=> 'تم العثور عليه في قاعدة بيانات الخدمة : منع المشاركات المُزعجة بالمنتدى',
));
