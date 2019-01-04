<?php
/**
 *
 * @package Upload Extensions Updater
 * @copyright (c) 2015 Igor Lavrov (https://github.com/LavIgor)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace boardtools\updater\includes\upload;

use \boardtools\updater\includes\objects;
use \boardtools\updater\includes\functions\files;

abstract class base
{
	/** @var string Temporary storage path */
	protected $ext_tmp;

	/**
	 * Returns the path to the temporary storage directory.
	 *
	 * @param bool $ext_relative Whether the path should be relative to ext/ directory
	 * @return string
	 */
	protected function get_temp_path($ext_relative = false)
	{
		$ext_path = ($ext_relative) ? '' : objects::$phpbb_root_path . 'ext/';
		return $ext_path . objects::$updater_ext_name . '/tmp/' . (int) objects::$user->data['user_id'];
	}

	/**
	 * Sets the path to the temporary storage directory.
	 *
	 * @param bool $clean Whether we need to delete any previous contents of temporary directory
	 * @return bool Whether the path has been set correctly with no errors
	 */
	protected function set_temp_path($clean = true)
	{
		// We need to use the user ID and the time to escape from problems with simultaneous uploads.
		// We suppose that one user can upload only one extension per session.
		$this->ext_tmp = $this->get_temp_path();

		// Ensure that we don't have any previous files in the working directory.
		if ($clean && is_dir($this->ext_tmp))
		{
			if (!(files::catch_errors(files::rrmdir($this->ext_tmp))))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Extracts the specified ZIP file to the temporary storage directory.
	 *
	 * @param string $dest_file Path to ZIP file that we need to extract
	 */
	protected function extract_zip($dest_file)
	{
		if (!class_exists('\compress_zip'))
		{
			include(objects::$phpbb_root_path . 'includes/functions_compress.' . objects::$phpEx);
		}

		$zip = new \compress_zip('r', $dest_file);
		$zip->extract($this->ext_tmp . '/extracted/');
		$zip->close();
	}

	/**
	 * Original copyright information for the function from AutoMOD.
	 * The function was almost totally changed by the authors of Upload Extensions.
	 * @package       automod
	 * @copyright (c) 2008 phpBB Group
	 * @license       http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 *
	 * @param string $action Requested action.
	 * @return \phpbb\files\filespec|\filespec|bool
	 */
	public function proceed_upload($action)
	{
		//$can_upload = (@ini_get('file_uploads') == '0' || strtolower(@ini_get('file_uploads')) == 'off' || !@extension_loaded('zlib')) ? false : true;

		objects::$user->add_lang('posting');  // For error messages
		$upload = objects::$compatibility->get_upload_object();
		$upload->set_allowed_extensions(array('zip'));    // Only allow ZIP files

		// Make sure the ext/ directory exists and if it doesn't, create it
		if (!is_dir(objects::$phpbb_root_path . 'ext'))
		{
			if (!files::catch_errors(files::recursive_mkdir(objects::$phpbb_root_path . 'ext')))
			{
				return false;
			}
		}

		if (!is_writable(objects::$phpbb_root_path . 'ext'))
		{
			files::catch_errors(objects::$user->lang['EXT_NOT_WRITABLE']);
			return false;
		}

		$tmp_dir = objects::$phpbb_root_path . 'ext/' . objects::$updater_ext_name . '/tmp';
		if (!is_writable($tmp_dir))
		{
			if (!phpbb_chmod($tmp_dir, CHMOD_READ | CHMOD_WRITE))
			{
				files::catch_errors(objects::$user->lang['EXT_TMP_NOT_WRITABLE']);
				return false;
			}
		}

		$file = false;

		// Proceed with the upload
		if ($action === 'upload_remote')
		{
			$php_ini = new \phpbb\php\ini();
			if (!$php_ini->get_bool('allow_url_fopen'))
			{
				files::catch_errors(objects::$user->lang['EXT_ALLOW_URL_FOPEN_DISABLED']);
				return false;
			}
			$remote_url = objects::$request->variable('remote_upload', '');
			if (!extension_loaded('openssl') && 'https' === substr($remote_url, 0, 5))
			{
				files::catch_errors(objects::$user->lang['EXT_OPENSSL_DISABLED']);
				return false;
			}
			$file = objects::$compatibility->remote_upload($upload, $remote_url);
		}

		return $file;
	}

	/**
	 * The function that uploads the specified extension.
	 *
	 * @param \phpbb\files\filespec|\filespec $file       Filespec object.
	 * @param string                          $upload_dir The directory for zip files storage.
	 * @return string|bool
	 */
	public function get_dest_file($file, $upload_dir)
	{
		if (!objects::$compatibility->filespec_get($file, 'filename'))
		{
			files::catch_errors((sizeof($file->error) ? implode('<br />', $file->error) : objects::$user->lang['NO_UPLOAD_FILE']));
			return false;
		}

		if (objects::$compatibility->filespec_get($file, 'init_error') || sizeof($file->error))
		{
			$file->remove();
			files::catch_errors((sizeof($file->error) ? implode('<br />', $file->error) : objects::$user->lang['EXT_UPLOAD_INIT_FAIL']));
			return false;
		}

		$file->clean_filename('real');
		$file->move_file(str_replace(objects::$phpbb_root_path, '', $upload_dir), true, true);

		if (sizeof($file->error))
		{
			$file->remove();
			files::catch_errors(implode('<br />', $file->error));
			return false;
		}
		$dest_file = objects::$compatibility->filespec_get($file, 'destination_file');

		return $dest_file;
	}
}
