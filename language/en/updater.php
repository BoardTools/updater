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
	'ACP_UPDATER_EXT_TITLE_EXPLAIN'		=> 'Upload Extensions Updater enables you to check the updates for Upload Extensions and install them without using FTP.',

	'EXT_UPLOAD_INIT_FAIL'				=> 'There was an error when initialising the extension upload process.',
	'EXT_NOT_WRITABLE'					=> 'The ext/ directory is not writable. This is required for “Upload extension” to work properly. Please adjust your permissions or settings and try again.',
	'EXT_UPLOAD_ERROR'					=> 'The extension wasn’t uploaded. Please confirm that you upload the true extension zip file and try again.',
	'EXT_TMP_NOT_WRITABLE'				=> 'The ext/boardtools/updater/tmp/ directory is not writable. This is required for “Upload Extensions” to work properly. Please adjust your permissions or settings and try again.',
	'EXT_ALLOW_URL_FOPEN_DISABLED'		=> 'The allow_url_fopen setting should be enabled in order to load information from a remote resource. Please confirm that the allow_url_fopen setting is enabled in your php.ini and try again.',
	'EXT_OPENSSL_DISABLED'				=> 'The openssl extension should be enabled in order to load information from an https resource. Please confirm that the openssl extension is enabled in your php.ini and try again.',
	'NO_UPLOAD_FILE'					=> 'No file specified or there was an error during the upload process.',
	'NOT_AN_EXTENSION'					=> 'The uploaded zip file is not a phpBB extension. The file was not saved on the server.',

	'STATUS'							=> 'Status',
	'UPLOAD_EXTENSIONS'					=> 'Upload Extensions',
	'UPDATER_UPLOAD_NOT_INSTALLED'		=> 'Upload Extensions is not installed on your board.',
	'UPDATER_UPLOAD_NO_NEW_UPDATES'		=> 'New updates are not available at the moment.',
	'UPDATER_UPLOAD_NEW_UPDATES'		=> 'New updates are ready to be installed.',
	'UPDATER_UPLOAD_NO_UPDATES_INFO'	=> 'Information about available updates could not be obtained.',

	'EXT_UPDATER_STEP_1'				=> '1. Check the availability of the new release.',
	'EXT_UPDATER_STEP_2'				=> '2. Install the updates.',
	'EXT_UPDATER_STEP_3'				=> '3. Enable the extension.',

	'SOURCE'							=> 'Source',
	'EXTENSION_UPDATE'					=> 'Update the extension',
	'EXTENSION_UPDATE_NO_LINK'			=> 'Download link is not provided.',
	'EXTENSION_TO_BE_ENABLED'			=> 'Upload Extensions will be disabled during the update process and enabled again after the update.',
	'EXT_ACTION_ERROR'					=> 'The requested action cannot be performed.',

	'ACP_UPLOAD_EXT_ERROR_DEST'			=> 'No vendor or destination folder in the uploaded zip file. The file was not saved on the server.',
	'ACP_UPLOAD_EXT_ERROR_COMP'			=> 'composer.json wasn’t found in the uploaded zip file. The file was not saved on the server.',
	'ACP_UPLOAD_EXT_ERROR_NOT_SAVED'	=> 'The file was not saved on the server.',
	'ACP_UPLOAD_EXT_WRONG_RESTORE'		=> 'An error occurred during the update of an installed extension. Try to update it again.',
	'ACP_UPLOAD_EXT_NOT_COMPATIBLE'		=> 'This extension enables you to update only Upload Extensions. It seems that you have tried to install an update for another extension. Please use Upload Extensions to update other extensions.',

	'EXT_ENABLE'						=> 'Enable',
	'EXT_ENABLED'						=> 'The extension was enabled successfully.',
	'EXT_ENABLED_LATEST'				=> 'The latest version of the extension was enabled successfully.',
	'EXT_UPLOADED'						=> 'The upload was successful.',

	'ERROR_COPY_FILE'					=> 'The attempt to copy the file “%1$s” to the location “%2$s” failed.',
	'ERROR_CREATE_DIRECTORY'			=> 'The attempt to create the directory “%s” failed.',
	'ERROR_REMOVE_DIRECTORY'			=> 'The attempt to remove the directory “%s” failed.',
));
