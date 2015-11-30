<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace boardtools\updater\acp;

use \boardtools\updater\includes\objects;
use \boardtools\updater\includes\functions\files;
use \boardtools\updater\includes\functions\extensions;

class updater_module
{
	public $u_action;
	public $updater_ext_name;
	function main($id, $mode)
	{
		global $config, $user, $cache, $template, $request, $phpbb_root_path, $phpEx, $phpbb_log, $phpbb_extension_manager;

		// General settings for displaying the page.
		$this->page_title = $user->lang['ACP_UPDATER_EXT_TITLE'];
		$this->tpl_name = 'acp_updater';
		$user->add_lang(array('install', 'acp/extensions', 'migrator'));
		$user->add_lang_ext('boardtools/updater', 'updater');

		// get any url vars
		$action = $request->variable('action', '');

		// Work with objects class instead of $this.
		objects::$cache = &$cache;
		objects::$config = &$config;
		objects::$log = &$phpbb_log;
		objects::$phpEx = $phpEx;
		objects::$phpbb_extension_manager = &$phpbb_extension_manager;
		objects::$phpbb_root_path = $phpbb_root_path;
		objects::$request = &$request;
		objects::$template = &$template;
		objects::$u_action = $this->u_action;
		objects::$user = &$user;

		objects::$upload_ext_name = 'boardtools/upload';
		$this->updater_ext_name = 'boardtools/updater';
		$this->get_updater_metadata();

		switch ($action)
		{
			case 'enable':
				if ($phpbb_extension_manager->is_enabled(objects::$upload_ext_name))
				{
					$template->assign_var('EXT_ENABLED', true);
				}
				else
				{
					extensions::enable(objects::$upload_ext_name);
				}
				break;

			case 'upload':
			case 'force_update':
				$this->upload_ext($action);
				break;
		}
		$this->get_upload_metadata();
		$template->assign_vars(array(
			'U_ACTION'				=> objects::$u_action,
			'U_VERSIONCHECK_FORCE'	=> objects::$u_action . '&amp;versioncheck_force=1',
		));
		$this->catch_errors();
	}

	protected function get_upload_metadata()
	{
		global $config, $template, $request, $user, $phpbb_root_path, $phpbb_extension_manager;
		// Get the information about Upload Extensions - START
		if ($phpbb_extension_manager->is_available(objects::$upload_ext_name))
		{
			$template->assign_vars(array(
				'UPLOAD_EXT_INSTALLED'	=> true,
				'UPLOAD_EXT_ENABLED'	=> $phpbb_extension_manager->is_enabled(objects::$upload_ext_name),
			));
			$upload_md_manager = new \phpbb\extension\metadata_manager(objects::$upload_ext_name, $config, $phpbb_extension_manager, $template, $user, $phpbb_root_path);
			try
			{
				$metadata = $upload_md_manager->get_metadata('all');
				$template->assign_var('UPLOAD_META_VERSION', $metadata['version']);
			}
			catch (\phpbb\extension\exception $e)
			{
				files::catch_errors($e);
			}

			try
			{
				$updates_available = extensions::version_check($upload_md_manager, true);

				$template->assign_vars(array(
					'UPLOAD_EXT_NEW_UPDATE' => !empty($updates_available),
					'S_UPLOAD_UP_TO_DATE'   => empty($updates_available),
					'S_UPLOAD_VERSIONCHECK' => true,
					'UPLOAD_UP_TO_DATE_MSG' => $user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $upload_md_manager->get_metadata('display-name')),
				));

				foreach ($updates_available as $branch => $version_data)
				{
					$version_data['update_link'] = $request->escape($version_data['download'], true);
					$template->assign_block_vars('upload_updates_available', $version_data);
				}
			}
			catch (\RuntimeException $e)
			{
				$template->assign_vars(array(
					'S_UPLOAD_VERSIONCHECK_STATUS'    => $e->getCode(),
					'UPLOAD_VERSIONCHECK_FAIL_REASON' => ($e->getMessage() !== $user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
				));
			}
		}
		// Get the information about Upload Extensions - END
	}

	protected function get_updater_metadata()
	{
		global $config, $template, $request, $user, $phpbb_root_path, $phpbb_extension_manager;
		$md_manager = new \phpbb\extension\metadata_manager($this->updater_ext_name, $config, $phpbb_extension_manager, $template, $user, $phpbb_root_path);
		try
		{
			$metadata = $md_manager->get_metadata('all');
			$template->assign_var('META_VERSION', $metadata['version']);
		}
		catch (\phpbb\extension\exception $e)
		{
			files::catch_errors($e);
		}

		try
		{
			$updates_available = extensions::version_check($md_manager, $request->variable('versioncheck_force', false));

			$template->assign_vars(array(
				'UPDATER_EXT_NEW_UPDATE'	=> !empty($updates_available),
				'S_UPDATER_UP_TO_DATE'		=> empty($updates_available),
				'S_UPDATER_VERSIONCHECK'	=> true,
				'UPDATER_UP_TO_DATE_MSG'	=> $user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
			));

			foreach ($updates_available as $branch => $version_data)
			{
				$template->assign_block_vars('updater_updates_available', $version_data);
			}
		}
		catch (\RuntimeException $e)
		{
			$template->assign_vars(array(
				'S_UPDATER_VERSIONCHECK_STATUS'			=> $e->getCode(),
				'UPDATER_VERSIONCHECK_FAIL_REASON'		=> ($e->getMessage() !== $user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
			));
		}
	}

	/**
	 * Displays the special template in a case of errors.
	 */
	protected function catch_errors()
	{
		if (files::$catched_errors)
		{
			objects::$template->assign_var("S_EXT_ERROR", true);
		}
	}

	/**
	 * Original copyright information for the function from AutoMOD.
	 * The function was almost totally changed by the authors of Upload Extensions.
	 * @package automod
	 * @copyright (c) 2008 phpBB Group
	 * @license http://opensource.org/licenses/gpl-2.0.php GNU Public License
	 *
	 * @param string $action Requested action.
	 * @return bool
	 */
	function upload_ext($action)
	{
		global $phpbb_root_path, $phpEx, $phpbb_log, $phpbb_extension_manager, $template, $user, $request;

		//$can_upload = (@ini_get('file_uploads') == '0' || strtolower(@ini_get('file_uploads')) == 'off' || !@extension_loaded('zlib')) ? false : true;

		$user->add_lang('posting');  // For error messages
		if (!class_exists('\fileupload'))
		{
			include($phpbb_root_path . 'includes/functions_upload.' . $phpEx);
		}
		$upload = new \fileupload();
		$upload->set_allowed_extensions(array('zip'));	// Only allow ZIP files

		$upload_dir = $phpbb_root_path . 'ext';
		// Make sure the ext/ directory exists and if it doesn't, create it
		if (!is_dir($phpbb_root_path . 'ext'))
		{
			files::catch_errors(files::recursive_mkdir($phpbb_root_path . 'ext'));
		}

		if (!is_writable($phpbb_root_path . 'ext'))
		{
			files::catch_errors($user->lang['EXT_NOT_WRITABLE']);
			return false;
		}

		// Proceed with the upload
		$file = files::remote_upload($upload, $request->variable('remote_upload', ''));

		// What is a safe limit of execution time? Half the max execution time should be safe.
		$safe_time_limit = (ini_get('max_execution_time') / 2);
		$start_time = time();
		// We skip working with a zip file if we are enabling/restarting the extension.
		if ($action != 'force_update')
		{
			if (empty($file->filename))
			{
				files::catch_errors((sizeof($file->error) ? implode('<br />', $file->error) : $user->lang['NO_UPLOAD_FILE']));
				return false;
			}
			else if ($file->init_error || sizeof($file->error))
			{
				$file->remove();
				files::catch_errors((sizeof($file->error) ? implode('<br />', $file->error) : $user->lang['EXT_UPLOAD_INIT_FAIL']));
				return false;
			}

			$file->clean_filename('real');
			$file->move_file(str_replace($phpbb_root_path, '', $upload_dir), true, true);

			if (sizeof($file->error))
			{
				$file->remove();
				files::catch_errors(implode('<br />', $file->error));
				return false;
			}
			$dest_file = $file->destination_file;

			if (!class_exists('\compress_zip'))
			{
				include($phpbb_root_path . 'includes/functions_compress.' . $phpEx);
			}

			// We need to use the user ID and the time to escape from problems with simultaneous uploads.
			// We suppose that one user can upload only one extension per session.
			$ext_tmp = $this->updater_ext_name . '/tmp/' . (int) $user->data['user_id'];
			// Ensure that we don't have any previous files in the working directory.
			if (is_dir($phpbb_root_path . 'ext/' . $ext_tmp))
			{
				if (!(files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp))))
				{
					$file->remove();
					return false;
				}
			}

			$zip = new \compress_zip('r', $dest_file);
			$zip->extract($phpbb_root_path . 'ext/' . $ext_tmp . '/');
			$zip->close();

			$composery = files::getComposer($phpbb_root_path . 'ext/' . $ext_tmp);
			if (!$composery)
			{
				files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
				$file->remove();
				files::catch_errors($user->lang['ACP_UPLOAD_EXT_ERROR_COMP']);
				return false;
			}
			$string = @file_get_contents($composery);
			if ($string === false)
			{
				files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
				$file->remove();
				files::catch_errors($user->lang['EXT_UPLOAD_ERROR']);
				return false;
			}
			$json_a = json_decode($string, true);
			$destination = (isset($json_a['name'])) ? $json_a['name'] : '';
			$ext_version = (isset($json_a['version'])) ? $json_a['version'] : '0.0.0';
			if (strpos($destination, '/') === false)
			{
				files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
				$file->remove();
				files::catch_errors($user->lang['ACP_UPLOAD_EXT_ERROR_DEST']);
				return false;
			}
			else if (strpos($destination, objects::$upload_ext_name) === false)
			{
				files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
				$file->remove();
				files::catch_errors($user->lang['ACP_UPLOAD_EXT_NOT_COMPATIBLE']);
				return false;
			}
			$display_name = (isset($json_a['extra']['display-name'])) ? $json_a['extra']['display-name'] : $destination;
			if (!isset($json_a['type']) || $json_a['type'] != "phpbb-extension")
			{
				files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
				$file->remove();
				files::catch_errors($user->lang['NOT_AN_EXTENSION']);
				return false;
			}
			$source = substr($composery, 0, -14);
			$source_for_check = $ext_tmp . '/' . $destination;
			// At first we need to change the directory structure to something like ext/tmp/vendor/extension.
			// We need it to escape from problems with dots on validation.
			if ($source != $phpbb_root_path . 'ext/' . $source_for_check)
			{
				if (!(files::catch_errors(files::rcopy($source, $phpbb_root_path . 'ext/' . $source_for_check))))
				{
					files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
					$file->remove();
					return false;
				}
				$source = $phpbb_root_path . 'ext/' . $source_for_check;
			}
			// Validate the extension to check if it can be used on the board.
			$md_manager = $phpbb_extension_manager->create_extension_metadata_manager($source_for_check, $template);
			try
			{
				if ($md_manager->get_metadata() === false || $md_manager->validate_require_phpbb() === false || $md_manager->validate_require_php() === false)
				{
					files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
					$file->remove();
					files::catch_errors($user->lang['EXTENSION_NOT_AVAILABLE']);
					return false;
				}
			}
			catch (\phpbb\extension\exception $e)
			{
				files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
				$file->remove();
				files::catch_errors($e . ' ' . $user->lang['ACP_UPLOAD_EXT_ERROR_NOT_SAVED']);
				return false;
			}

			// Here we can assume that all checks are done.
			// Now we are able to install the uploaded extension to the correct path.
		}
		else
		{
			// All checks were done previously. Now we only need to restore the variables.
			// We try to restore the data of the current upload.
			$ext_tmp =  $this->updater_ext_name . '/tmp/' . (int) $user->data['user_id'];
			if (!is_dir($phpbb_root_path . 'ext/' . $ext_tmp) || !($composery = files::getComposer($phpbb_root_path . 'ext/' . $ext_tmp)) || !($string = @file_get_contents($composery)))
			{
				files::catch_errors($user->lang['ACP_UPLOAD_EXT_WRONG_RESTORE']);
				return false;
			}
			$json_a = json_decode($string, true);
			$destination = (isset($json_a['name'])) ? $json_a['name'] : '';
			if (strpos($destination, '/') === false)
			{
				files::catch_errors($user->lang['ACP_UPLOAD_EXT_WRONG_RESTORE']);
				return false;
			}
			$source = substr($composery, 0, -14);
			$display_name = (isset($json_a['extra']['display-name'])) ? $json_a['extra']['display-name'] : $destination;
		}
		$made_update = false;
		// Delete the previous version of extension files - we're able to update them.
		if (is_dir($phpbb_root_path . 'ext/' . $destination))
		{
			// At first we need to disable the extension if it is enabled.
			if ($phpbb_extension_manager->is_enabled($destination))
			{
				while ($phpbb_extension_manager->disable_step($destination))
				{
					// Are we approaching the time limit? If so, we want to pause the update and continue after refreshing.
					if ((time() - $start_time) >= $safe_time_limit)
					{
						$template->assign_var('S_NEXT_STEP', objects::$user->lang['EXTENSION_DISABLE_IN_PROGRESS']);

						// No need to specify the name of the extension. We suppose that it is the one in ext/tmp/USER_ID folder.
						meta_refresh(0, objects::$u_action . '&amp;action=force_update');
						return false;
					}
				}
				$phpbb_log->add('admin', $user->data['user_id'], $user->ip, 'LOG_EXT_DISABLE', time(), array($destination));
				$made_update = true;
			}
			if (!(files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $destination))))
			{
				return false;
			}
		}
		if (!(files::catch_errors(files::rcopy($source, $phpbb_root_path . 'ext/' . $destination))))
		{
			files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp));
			return false;
		}
		// No enabling at this stage. Admins should have a chance to revise the uploaded scripts.
		if (!(files::catch_errors(files::rrmdir($phpbb_root_path . 'ext/' . $ext_tmp))))
		{
			return false;
		}

		if ($made_update)
		{
			// We have updated Upload Extensions - let's enable it again.
			redirect(objects::$u_action . '&action=enable');
		}
		else
		{
			$template->assign_var('EXT_UPLOADED', true);
		}

		return true;
	}
}
