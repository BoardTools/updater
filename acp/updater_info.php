<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace boardtools\updater\acp;

class updater_info
{
	function module()
	{
		return array(
			'filename'    => 'boardtools\updater\acp\updater_module',
			'title'        => 'ACP_UPDATER_EXT_TITLE',
			'version'    => '1.0.0',
			'modes'        => array(
				'main'		=> array(
											'title'	=> 'ACP_UPDATER_EXT_CONFIG_TITLE',
											'auth'	=> 'ext_boardtools/updater && acl_a_extensions',
											'cat'	=> array('ACP_EXTENSION_MANAGEMENT')
									),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}
