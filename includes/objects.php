<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 - 2019 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace boardtools\updater\includes;

/**
* The class of objects and global variables.
*/
class objects
{
	/** @var \phpbb\cache\service */
	public static $cache;

	/** @var \boardtools\updater\includes\compatibility\base */
	public static $compatibility;

	/** @var \phpbb\config\config */
	public static $config;

	/** @var \phpbb\log\log */
	public static $log;

	/** @var string phpEx */
	public static $phpEx;

	/** @var \Symfony\Component\DependencyInjection\ContainerBuilder phpbb_container */
	public static $phpbb_container;

	/** @var \phpbb\extension\manager */
	public static $phpbb_extension_manager;

	/** @var string phpbb_root_path */
	public static $phpbb_root_path;

	/** @var \phpbb\request\request */
	public static $request;

	/** @var \phpbb\template\template */
	public static $template;

	/** @var string u_action */
	public static $u_action;

	/** @var string updater_ext_name - the name of Upload Extensions Updater */
	public static $updater_ext_name;

	/** @var \phpbb\files\upload */
	public static $upload;

	/** @var string upload_ext_name - the name of Upload Extensions */
	public static $upload_ext_name;

	/** @var \phpbb\user */
	public static $user;

	public static function get_phpbb_branch()
	{
		static $branch = null;
		if (is_null($branch))
		{
			preg_match('/^(\d+\.\d+).+/', static::$config['version'], $matches);
			$branch = $matches[1];
		}
		return $branch;
	}

	public static function set_compatibility_class()
	{
		$branch = static::get_phpbb_branch();
		switch ($branch)
		{
			case '3.2':
				static::$compatibility = new compatibility\v_3_2_x();
			break;
			default:
				static::$compatibility = new compatibility\v_3_1_x();
			break;
		}
		static::$compatibility->init();
	}
}
