<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
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

$lang = array_merge($lang, array(
	'ACP_UPDATER_EXT_TITLE'				=> 'أداة تحديث "رفع الإضافات"',
	'ACP_UPDATER_EXT_CONFIG_TITLE'		=> 'أداة تحديث "رفع الإضافات"',
	'ACP_UPDATER_EXT_TITLE_EXPLAIN'		=> 'أداة تحديث "رفع الإضافات" تعطيك إمكانية التحقق من وجود إصدار جديد للإضافة "رفع الإضافات" وتحديثها بدون استخدام برنامج نقل البيانات FTP.',

	'EXT_UPLOAD_INIT_FAIL'				=> 'هناك مشكلة عند الإعداد لعملية تحديث الإضافة.',
	'EXT_NOT_WRITABLE'					=> 'المجلد ext/ غير قابل للكتابة وبالتالي لا يُمكن أن تعمل الإضافة "رفع الإضافات" بصورة صحيحة. الرجاء إعطاء المجلد ext تصريح الكتابة أو ضبط الإعدادات والمحاولة مرة أخرى',
	'EXT_UPLOAD_ERROR'					=> 'لم يتم رفع الإضافة. نرجوا التأكد من صحة الملف المضغوط للإضافة المطلوبة والمحاولة مرة أخرى.',
	'NO_UPLOAD_FILE'					=> 'لم يتم تحديد أي ملف أو هناك خطأ أثناء عملية رفع الإضافة.',
	'NOT_AN_EXTENSION'					=> 'لم يتم التعرف على الملف المضغوط الذي رفعته. لم يتم حفظ الملف في السيرفر.',

	'STATUS'							=> 'الحالة ',
	'UPLOAD_EXTENSIONS'					=> 'رفع الإضافات',
	'UPDATER_UPLOAD_NOT_INSTALLED'		=> 'الإضافة "رفع الإضافات" غير مُثبتة في منتداك.',
	'UPDATER_UPLOAD_NO_NEW_UPDATES'		=> 'لا توجد تحديثات جديدة حالياً.',
	'UPDATER_UPLOAD_NEW_UPDATES'		=> 'هناك تحديثات جديدة جاهزة للتثبيت.',
	'UPDATER_UPLOAD_NO_UPDATES_INFO'	=> 'لا يمكن الحصول على معلومات عن توفر تحديثات جديدة .',

	'EXT_UPDATER_STEP_1'				=> '1. التحقق من وجود إصدار جديد.',
	'EXT_UPDATER_STEP_2'				=> '2. تثبيت التحديثات.',
	'EXT_UPDATER_STEP_3'				=> '3. تفعيل الإضافة.',

	'SOURCE'							=> 'المصدر',
	'EXTENSION_UPDATE'					=> 'تحديث الإضافة',
	'EXTENSION_UPDATE_NO_LINK'			=> 'رابط التحميل غير متوفر.',
	'EXTENSION_TO_BE_ENABLED'			=> 'سوف يتم تعطيل "رفع الإضافات" أثناء عملية التحديث وإعادة التفعيل بعد التحديث..',
	'EXT_ACTION_ERROR'					=> 'لا يُمكن تنفيذ العملية التي طلبتها.',

	'ACP_UPLOAD_EXT_ERROR_DEST'			=> 'مجلد ال vendor أو مسار المجلد غير موجود في الملف المضغوط الذي رفعته. لم يتم حفظ الملف في السيرفر.',
	'ACP_UPLOAD_EXT_ERROR_COMP'			=> 'الملف composer.json غير موجود في الملف المضغوط الذي رفعته. لم يتم حفظ الملف في السيرفر.',
	'ACP_UPLOAD_EXT_ERROR_NOT_SAVED'	=> 'لم يتم حفظ الملف في السيرفر.',
	'ACP_UPLOAD_EXT_WRONG_RESTORE'		=> 'حدثت مشكلة أثناء عملية تحديث إضافة مُثبتة في منتداك. نرجوا المحاولة مرة أخرى.',
	'ACP_UPLOAD_EXT_NOT_COMPATIBLE'		=> 'وظيفة هذه الإضافة هي تحديث "رفع الإضافات" فقط. ويبدوا أنك حاولت تحديث إضافة أخرى. الرجاء أستخدام "رفع الإضافات" لتحديث الإضافات الأخرى.',

	'EXT_ENABLE'						=> 'تفعيل',
	'EXT_ENABLED'						=> 'تم تفعيل الإضافة بنجاح.',
	'EXT_ENABLED_LATEST'				=> 'تم تفعيل آخر نسخة للإضافة بنجاح.',
	'EXT_UPLOADED'						=> 'تمت عملية الرفع بنجاح.',

	'ERROR_COPY_FILE'					=> 'فشلت المحاولة لنسخ الملف “%1$s” إلى “%2$s”.',
	'ERROR_CREATE_DIRECTORY'			=> 'فشلت المحاولة لإنشاء المجلد “%s”.',
	'ERROR_REMOVE_DIRECTORY'			=> 'فشلت المحاولة لإزالة المجلد “%s”.',
));
