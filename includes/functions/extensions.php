<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 - 2019 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace boardtools\updater\includes\functions;

use \boardtools\updater\includes\objects;

class extensions
{
	/**
	* The function that gets the manager for the specified extension.
	* @param string $ext_name The name of the extension.
	* @return \phpbb\extension\metadata_manager|bool
	*/
	public static function get_manager($ext_name)
	{
		// If they've specified an extension, let's load the metadata manager and validate it.
		if ($ext_name && $ext_name === objects::$upload_ext_name)
		{
			$md_manager = objects::$compatibility->create_metadata_manager($ext_name);

			try
			{
				$md_manager->get_metadata('all');
			}
			catch (\phpbb\extension\exception $e)
			{
				$message = objects::$compatibility->get_exception_message($e);
				files::catch_errors($message);
				return false;
			}
			return $md_manager;
		}
		files::catch_errors(objects::$user->lang['EXT_ACTION_ERROR']);
		return false;
	}

	/**
	* The function that enables the specified extension.
	* @param string $ext_name The name of the extension.
	* @return bool
	*/
	public static function enable($ext_name)
	{
		// What is a safe limit of execution time? Half the max execution time should be safe.
		$safe_time_limit = (ini_get('max_execution_time') / 2);
		$start_time = time();

		$md_manager = self::get_manager($ext_name);

		if ($md_manager === false)
		{
			return false;
		}

		if (!$md_manager->validate_dir())
		{
			files::catch_errors(objects::$user->lang['EXTENSION_DIR_INVALID']);
			return false;
		}

		if (!$md_manager->validate_enable())
		{
			files::catch_errors(objects::$user->lang['EXTENSION_NOT_AVAILABLE']);
			return false;
		}

		$extension = objects::$phpbb_extension_manager->get_extension($ext_name);
		if (!$extension->is_enableable())
		{
			files::catch_errors(objects::$user->lang['EXTENSION_NOT_ENABLEABLE']);
			return false;
		}

		if (objects::$phpbb_extension_manager->is_enabled($ext_name))
		{
			return true;
		}

		try
		{
			while (objects::$phpbb_extension_manager->enable_step($ext_name))
			{
				// Are we approaching the time limit? If so we want to pause the update and continue after refreshing
				if ((time() - $start_time) >= $safe_time_limit)
				{
					objects::$template->assign_var('S_NEXT_STEP', objects::$user->lang['EXTENSION_ENABLE_IN_PROGRESS']);
					meta_refresh(0, objects::$u_action . '&amp;action=enable');
					return false;
				}
			}
			objects::$log->add('admin', objects::$user->data['user_id'], objects::$user->ip, 'LOG_EXT_ENABLE', time(), array($ext_name));
		}
		catch (\phpbb\db\migration\exception $e)
		{
			files::catch_errors($e->getLocalisedMessage(objects::$user));
			return false;
		}
		// Make the Upload Extensions menu link displayed in the ACP.
		redirect(objects::$u_action . '&action=enable');
		return true;
	}
}
