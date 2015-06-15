<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace boardtools\updater\migrations;

class install_updater extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return isset($this->config['updater_version']) && version_compare($this->config['updater_version'], '1.0.0', '>=');
	}

	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v310\dev');
	}

	public function update_data()
	{
		return array(
			array('config.add', array('updater_version', '1.0.0')),
			array('module.add', array(
				'acp', 'ACP_EXTENSION_MANAGEMENT', array(
					'module_basename'	=> '\boardtools\updater\acp\updater_module',
					'auth'				=> 'ext_boardtools/updater && acl_a_extensions',
					'modes'				=> array('main'),
				),
			)),
		);
	}
}
