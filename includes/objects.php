<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 Igor Lavrov (https://github.com/LavIgor)
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

	/** @var \phpbb\config\config */
	public static $config;

	/** @var \phpbb\log\log */
	public static $log;

	/** @var string phpEx */
	public static $phpEx;

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

	/** @var string upload_ext_name - the name of Upload Extensions */
	public static $upload_ext_name;

	/** @var \phpbb\user */
	public static $user;
}
