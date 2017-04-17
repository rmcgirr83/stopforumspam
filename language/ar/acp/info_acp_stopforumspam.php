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
//
// Some characters for use
// ’ » “ ” …


$lang = array_merge($lang, array(

	// ACP entries
	'ACP_SFS_TITLE'			=> 'منع المشاركات المُزعجة بالمنتدى',
	'SFS_CONTROL'			=> 'الإعدادات',
	'SFS_VERSION'			=> 'رقم النسخة :',
	'SFS_SETTINGS'			=> 'الإعدادات',
	'SFS_ENABLED'			=> 'تفعيل ',
	'SFS_ENABLED_EXPLAIN'	=> 'اختيارك "نعم" يعني تفعيل هذا الخيار. سيتم استخدام هذه الإضافة عند تسجيل عضو جديد وكذلك مُشاركات الزائر.',
	'SFS_THRESHOLD_SCORE'	=> 'الفحص ',
	'SFS_THRESHOLD_SCORE_EXPLAIN'	=> 'الإضافة سوف تفحص عدد المرات التي تم العثور على إسم المستخدم أو البريد الإلكتروني أو رقم عنوان الآي بي في قاعدة بيانات الخدمة : منع المشاركات المُزعجة بالمنتدى. تستطيع إضافة أي عدد من 1 إلى 99. العدد الأقل يعني إمكانية أكبر في العثور على نتيجة أفضل.',
	'SFS_LOG_MESSAGE'			=> 'قيد السجلات ',
	'SFS_LOG_MESSAGE_EXPLAIN'	=> 'اختيارك "نعم" يعني إضافة سجل الحركة لهذه الإضافة إلى سجلات المدير أو سجلات العضو في لوحة التحكم الرئيسية.',
	'SFS_BAN_IP'			=> 'حظر رقم الآي بي ',
	'SFS_BAN_IP_EXPLAIN'	=> 'اختيارك "نعم" يعني حظر رقم الآي بي IP الخاصة بالأعضاء لمدة ساعة واحدة',
	'SFS_BAN_REASON'		=> 'عرض سبب الحظر ',
	'SFS_BAN_REASON_EXPLAIN'	=> 'إذا أخترت "نعم" لحظر رقم الآي بي في الخيار أعلاه , تستطيع هنا تحديد عرض رسالة للعضو المحظور أو لا.',
	'SFS_DOWN'				=> 'السماح إذا تعطل سيرفر الخدمة ',
	'SFS_DOWN_EXPLAIN'		=> 'إستمرار عملية التسجيل والمُشاركة حتى لو توقف سيرفر الخدمة في www.stopforumspam.com.',
	'SFS_API_KEY'			=> 'مفتاح الـ API ',
	'SFS_API_KEY_EXPLAIN'	=> 'إذا تريد إضافة الأشخاص المُزعجين إلى قاعدة بيانات الموقع www.stopforumspam.com , يجب عليك إضافة مفتاح التأكيد API الخاص بك <a target="_new" href="http://www.stopforumspam.com/keys">من هنا</a>. يجب عليك التسجيل أولاً بالموقع للحصول على المفتاح',
	// ACP message logs
	'LOG_SFS_MESSAGE'			=> '<strong>الإضافة "منع المشاركات المُزعجة بالمنتدى" قيدت</strong> :<br />إسم المستخدم : %1$s<br />رقم الـIP: %2$s<br />البريد الإلكتروني : %3$s',
	'LOG_SFS_DOWN'			=> '<strong>سيرفر الخدمة "منع المشاركات المُزعجة بالمنتدى" كان مُعطل خلال عملية تسجيل أو مُشاركة بالمنتدى</strong>',
	'LOG_SFS_DOWN_USER_ALLOWED' => '<strong>سيرفر الخدمة "منع المشاركات المُزعجة بالمنتدى" كان مُعطل.</strong> تم السماح للعضو التالي في المنتدى :<br />إسم المستخدم : %1$s<br />رقم الـIP :%2$s<br />البريد الإلكتروني : %3$s',
	'LOG_SFS_NEED_CURL'		=> 'الإضافة "منع المشاركات المُزعجة بالمنتدى" بحاجة إلى مكتبة اتصال السيرفرات <strong>cURL</strong> لكي تعمل بشكل صحيح. نرجوا الإتصال بالشركة المستضيفة لديك لتثبيت مكتبة cURL وتفعيلها.',
	'SFS_BY_NAME'	=> 'فحص إسم المستخدم ',
	'SFS_BY_EMAIL'	=> 'فحص البريد الإلكتروني ',
	'SFS_BY_IP'		=> 'فحص رقم الآي بي ',
	'TOO_SMALL_SFS_THRESHOLD'	=> 'قيمة الفحص التي أدخلتها صغيرة جداً.',
	'TOO_LARGE_SFS_THRESHOLD'	=> 'قيمة الفحص التي أدخلتها كبيرة جداً.',
	'SFS_SETTINGS_ERROR'		=> 'هناك خطأ حدث أثناء حفظ الإعدادات. نرجوا مُتابعة تقرير هذا الخطأ.',
	'SFS_SETTINGS_SUCCESS'		=> 'تم حفظ الإعدادات بنجاح.',
));
