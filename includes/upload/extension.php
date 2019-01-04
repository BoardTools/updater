<?php
/**
 *
 * @package Upload Extensions Updater
 * @copyright (c) 2015 - 2019 Igor Lavrov (https://github.com/LavIgor)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace boardtools\updater\includes\upload;

use \boardtools\updater\includes\objects;
use \boardtools\updater\includes\functions\files;

class extension extends base
{
	/** @var string Extension destination path (vendor/name) */
	protected $destination;

	/** @var string Extension temporary source path */
	protected $source;

	/**
	 * The function that uploads the specified extension.
	 *
	 * @param string $action Requested action.
	 * @return bool
	 */
	public function upload($action)
	{
		$file = $this->proceed_upload($action);
		if (!$file && $action != 'force_update')
		{
			files::catch_errors(objects::$user->lang['EXT_UPLOAD_ERROR']);
			return false;
		}

		// What is a safe limit of execution time? Half the max execution time should be safe.
		$safe_time_limit = (ini_get('max_execution_time') / 2);
		$start_time = time();

		// We skip working with a zip file if we are enabling/restarting the extension.
		if ($action != 'force_update')
		{
			$dest_file = $this->get_dest_file($file, objects::$phpbb_root_path . 'ext');
			if (!$dest_file)
			{
				files::catch_errors(objects::$user->lang['EXT_UPLOAD_ERROR']);
				return false;
			}

			if (!$this->set_temp_path())
			{
				$file->remove();
				return false;
			}

			$this->extract_zip($dest_file);

			$composery = files::getComposer($this->ext_tmp);
			if (!$composery)
			{
				files::catch_errors(files::rrmdir($this->ext_tmp));
				$file->remove();
				files::catch_errors(objects::$user->lang['ACP_UPLOAD_EXT_ERROR_COMP']);
				return false;
			}
			$string = @file_get_contents($composery);
			if ($string === false)
			{
				files::catch_errors(files::rrmdir($this->ext_tmp));
				$file->remove();
				files::catch_errors(objects::$user->lang['EXT_UPLOAD_ERROR']);
				return false;
			}
			$json_a = json_decode($string, true);
			$destination = (isset($json_a['name'])) ? $json_a['name'] : '';
			$destination = str_replace('.', '', $destination);

			if (!$this->check_ext_name($destination))
			{
				$file->remove();
				return false;
			}

			if (!isset($json_a['type']) || $json_a['type'] != "phpbb-extension")
			{
				files::catch_errors(files::rrmdir($this->ext_tmp));
				$file->remove();
				files::catch_errors(objects::$user->lang['NOT_AN_EXTENSION']);
				return false;
			}
			$source = substr($composery, 0, -14);

			// Try to use the extracted path if it contains the necessary directory structure.
			$source_for_check = $this->get_temp_path(true) . '/extracted/' . $destination;

			// At first we need to change the directory structure to something like ext/tmp/vendor/extension.
			// We need it to escape from problems with dots on validation.
			if ($source != objects::$phpbb_root_path . 'ext/' . $source_for_check)
			{
				$source_for_check = $this->get_temp_path(true) . '/uploaded/' . $destination;
				if (!(files::catch_errors(files::rcopy($source, objects::$phpbb_root_path . 'ext/' . $source_for_check))))
				{
					files::catch_errors(files::rrmdir($this->ext_tmp));
					$file->remove();
					return false;
				}
				$source = objects::$phpbb_root_path . 'ext/' . $source_for_check;
			}

			// Validate the extension to check if it can be used on the board.
			$md_manager = objects::$compatibility->create_metadata_manager($source_for_check);
			try
			{
				if ($md_manager->get_metadata() === false || $md_manager->validate_require_phpbb() === false || $md_manager->validate_require_php() === false)
				{
					files::catch_errors(files::rrmdir($this->ext_tmp));
					$file->remove();
					files::catch_errors(objects::$user->lang['EXTENSION_NOT_AVAILABLE']);
					return false;
				}
			}
			catch (\phpbb\extension\exception $e)
			{
				$message = objects::$compatibility->get_exception_message($e);
				files::catch_errors(files::rrmdir($this->ext_tmp));
				$file->remove();
				files::catch_errors($message . ' ' . objects::$user->lang['ACP_UPLOAD_EXT_ERROR_NOT_SAVED']);
				return false;
			}

			// Remove the uploaded archive file.
			$file->remove();

			// Here we can assume that all checks are done.
			// Now we are able to install the uploaded extension to the correct path.
		}
		else
		{
			// All checks were done previously. Now we only need to restore the variables.
			// We try to restore the data of the current upload.
			$this->set_temp_path(false);
			if (!is_dir($this->ext_tmp) || !($composery = files::getComposer($this->ext_tmp)) || !($string = @file_get_contents($composery)))
			{
				files::catch_errors(objects::$user->lang['ACP_UPLOAD_EXT_WRONG_RESTORE']);
				return false;
			}
			$json_a = json_decode($string, true);
			$destination = (isset($json_a['name'])) ? $json_a['name'] : '';
			$destination = str_replace('.', '', $destination);
			if (strpos($destination, '/') === false)
			{
				files::catch_errors(objects::$user->lang['ACP_UPLOAD_EXT_WRONG_RESTORE']);
				return false;
			}
			$source = substr($composery, 0, -14);
		}
		$made_update = false;
		// Delete the previous version of extension files - we're able to update them.
		if (is_dir(objects::$phpbb_root_path . 'ext/' . $destination))
		{
			// At first we need to disable the extension if it is enabled.
			if (objects::$phpbb_extension_manager->is_enabled($destination))
			{
				while (objects::$phpbb_extension_manager->disable_step($destination))
				{
					// Are we approaching the time limit? If so, we want to pause the update and continue after refreshing.
					if ((time() - $start_time) >= $safe_time_limit)
					{
						objects::$template->assign_var('S_NEXT_STEP', objects::$user->lang['EXTENSION_DISABLE_IN_PROGRESS']);

						// No need to specify the name of the extension. We suppose that it is the one in ext/tmp/USER_ID folder.
						meta_refresh(0, objects::$u_action . '&amp;action=force_update');
						return false;
					}
				}
				objects::$log->add('admin', objects::$user->data['user_id'], objects::$user->ip, 'LOG_EXT_DISABLE', time(), array($destination));
				$made_update = true;
			}

			if (!(files::catch_errors(files::rrmdir(objects::$phpbb_root_path . 'ext/' . $destination))))
			{
				return false;
			}
		}
		if (!(files::catch_errors(files::rcopy($source, objects::$phpbb_root_path . 'ext/' . $destination))))
		{
			files::catch_errors(files::rrmdir($this->ext_tmp));
			return false;
		}
		// No enabling at this stage. Admins should have a chance to revise the uploaded scripts.
		if (!(files::catch_errors(files::rrmdir($this->ext_tmp))))
		{
			return false;
		}

		// Clear phpBB cache after all the work has been done.
		// Needed because some files like ext.php can be deleted in the new version.
		// Should be done at last because we need to remove class names from data_global cache file.
		objects::$cache->purge();

		if ($made_update)
		{
			// We have updated Upload Extensions - let's enable it again.
			redirect(objects::$u_action . '&action=enable');
		}
		else
		{
			objects::$template->assign_var('EXT_UPLOADED', true);
		}

		return true;
	}

	protected function check_ext_name($destination)
	{
		if (strpos($destination, '/') === false)
		{
			files::catch_errors(files::rrmdir($this->ext_tmp));
			files::catch_errors(objects::$user->lang['ACP_UPLOAD_EXT_ERROR_DEST']);
			return false;
		}

		if (strpos($destination, objects::$upload_ext_name) === false)
		{
			files::catch_errors(files::rrmdir($this->ext_tmp));
			files::catch_errors(objects::$user->lang['ACP_UPLOAD_EXT_NOT_COMPATIBLE']);
			return false;
		}

		return true;
	}
}
