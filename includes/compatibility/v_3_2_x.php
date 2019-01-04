<?php
/**
 *
 * @package Upload Extensions Updater
 * @copyright (c) 2015 Igor Lavrov (https://github.com/LavIgor)
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace boardtools\updater\includes\compatibility;

use \boardtools\updater\includes\objects;

class v_3_2_x implements base
{
	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		objects::$upload = objects::$phpbb_container->get('files.upload');
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_exception_message($e)
	{
		return call_user_func_array(array(objects::$user, 'lang'), array_merge(array($e->getMessage()), $e->get_parameters()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_upload_object()
	{
		return objects::$upload;
	}

	/**
	 * {@inheritdoc}
	 */
	public function remote_upload($upload, $remote_url)
	{
		/** @var \boardtools\upload\includes\types\zip */
		$upload_zip = new \boardtools\upload\includes\types\zip(
			objects::$phpbb_container->get('files.factory'),
			objects::$phpbb_container->get('language'),
			objects::$phpbb_container->get('php_ini'),
			objects::$phpbb_container->get('request'),
			objects::$phpbb_container->getParameter('core.root_path')
		);
		$upload_zip->set_upload(objects::$upload);

		return $upload_zip->upload($remote_url);
	}

	/**
	 * {@inheritdoc}
	 */
	public function escape($var, $multibyte)
	{
		return objects::$request->escape($var, $multibyte);
	}

	/**
	 * {@inheritdoc}
	 */
	public function filespec_get($file, $param)
	{
		switch ($param)
		{
			case 'init_error':
				return $file->init_error();
			break;
			case 'filename':
			case 'destination_file':
				return $file->get($param);
			break;
		}
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_metadata_manager($name)
	{
		return objects::$phpbb_extension_manager->create_extension_metadata_manager($name);
	}

	/**
	 * {@inheritdoc}
	 */
	public function version_check(\phpbb\extension\metadata_manager $md_manager, $force_update = false, $force_cache = false, $stability = null)
	{
		return objects::$phpbb_extension_manager->version_check($md_manager, $force_update, $force_cache, $stability);
	}
}
