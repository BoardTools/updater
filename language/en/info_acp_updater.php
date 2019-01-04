<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 - 2019 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
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
	'ACP_UPDATER_EXT_TITLE'				=> 'Upload Extensions Updater',
	'ACP_UPDATER_EXT_CONFIG_TITLE'		=> 'Upload Extensions Updater',
));
