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
use \boardtools\updater\includes\upload\extension;

class updater_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	function main($id, $mode)
	{
		global $config, $user, $cache, $template, $request, $phpbb_root_path, $phpEx, $phpbb_log, $phpbb_extension_manager, $phpbb_container;

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
		objects::$phpbb_container = &$phpbb_container;
		objects::$phpbb_extension_manager = &$phpbb_extension_manager;
		objects::$phpbb_root_path = $phpbb_root_path;
		objects::$request = &$request;
		objects::$template = &$template;
		objects::$u_action = $this->u_action;
		objects::$user = &$user;

		// Add support for different phpBB branches.
		objects::set_compatibility_class();

		objects::$upload_ext_name = 'boardtools/upload';
		objects::$updater_ext_name = 'boardtools/updater';
		$this->get_updater_metadata();

		switch ($action)
		{
			case 'enable':
				if (objects::$phpbb_extension_manager->is_enabled(objects::$upload_ext_name))
				{
					objects::$template->assign_var('EXT_ENABLED', true);
				}
				else
				{
					extensions::enable(objects::$upload_ext_name);
				}
				break;

			case 'upload':
			case 'force_update':
				$extension = new extension();
				$extension->upload($action);
				break;
		}
		$this->get_upload_metadata();
		objects::$template->assign_vars(array(
			'U_ACTION'				=> objects::$u_action,
			'U_VERSIONCHECK_FORCE'	=> objects::$u_action . '&amp;versioncheck_force=1',
		));
		$this->catch_errors();
	}

	protected function get_upload_metadata()
	{
		// Get the information about Upload Extensions - START
		if (objects::$phpbb_extension_manager->is_available(objects::$upload_ext_name))
		{
			objects::$template->assign_vars(array(
				'UPLOAD_EXT_INSTALLED'	=> true,
				'UPLOAD_EXT_ENABLED'	=> objects::$phpbb_extension_manager->is_enabled(objects::$upload_ext_name),
			));
			$upload_md_manager = objects::$compatibility->create_metadata_manager(objects::$upload_ext_name);
			try
			{
				$metadata = $upload_md_manager->get_metadata('all');
				objects::$template->assign_var('UPLOAD_META_VERSION', $metadata['version']);
			}
			catch (\phpbb\extension\exception $e)
			{
				$message = objects::$compatibility->get_exception_message($e);
				files::catch_errors($message);
			}

			try
			{
				$updates_available = objects::$compatibility->version_check($upload_md_manager, true);

				objects::$template->assign_vars(array(
					'UPLOAD_EXT_NEW_UPDATE' => !empty($updates_available),
					'S_UPLOAD_UP_TO_DATE'   => empty($updates_available),
					'S_UPLOAD_VERSIONCHECK' => true,
					'UPLOAD_UP_TO_DATE_MSG' => objects::$user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $upload_md_manager->get_metadata('display-name')),
				));

				if (!empty($updates_available))
				{
					$updates_available['update_link'] = objects::$compatibility->escape($updates_available['download'], true);
				}

				objects::$template->assign_block_vars('upload_updates_available', $updates_available);
			}
			catch (\RuntimeException $e)
			{
				objects::$template->assign_vars(array(
					'S_UPLOAD_VERSIONCHECK_STATUS'    => $e->getCode(),
					'UPLOAD_VERSIONCHECK_FAIL_REASON' => ($e->getMessage() !== objects::$user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
				));
			}
		}
		// Get the information about Upload Extensions - END
	}

	protected function get_updater_metadata()
	{
		$md_manager = objects::$compatibility->create_metadata_manager(objects::$updater_ext_name);
		try
		{
			$metadata = $md_manager->get_metadata('all');
			objects::$template->assign_var('META_VERSION', $metadata['version']);
		}
		catch (\phpbb\extension\exception $e)
		{
			$message = objects::$compatibility->get_exception_message($e);
			files::catch_errors($message);
		}

		try
		{
			$updates_available = objects::$compatibility->version_check($md_manager, objects::$request->variable('versioncheck_force', false));

			objects::$template->assign_vars(array(
				'UPDATER_EXT_NEW_UPDATE'	=> !empty($updates_available),
				'S_UPDATER_UP_TO_DATE'		=> empty($updates_available),
				'S_UPDATER_VERSIONCHECK'	=> true,
				'UPDATER_UP_TO_DATE_MSG'	=> objects::$user->lang(empty($updates_available) ? 'UP_TO_DATE' : 'NOT_UP_TO_DATE', $md_manager->get_metadata('display-name')),
			));

			objects::$template->assign_block_vars('updater_updates_available', $updates_available);
		}
		catch (\RuntimeException $e)
		{
			objects::$template->assign_vars(array(
				'S_UPDATER_VERSIONCHECK_STATUS'		=> $e->getCode(),
				'UPDATER_VERSIONCHECK_FAIL_REASON'	=> ($e->getMessage() !== objects::$user->lang('VERSIONCHECK_FAIL')) ? $e->getMessage() : '',
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
}
