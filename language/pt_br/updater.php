<?php
/**
*
* @package Upload Extensions Updater
* @copyright (c) 2015 - 2019 Igor Lavrov (https://github.com/LavIgor)
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
* Brazilian Portuguese translation by eunaumtenhoid (c) 2019 [ver 1.1.0] (https://github.com/phpBBTraducoes)
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ACP_UPDATER_EXT_TITLE'				=> 'Atualizador do Upload Extensions',
	'ACP_UPDATER_EXT_CONFIG_TITLE'		=> 'Atualizador do Upload Extensions',
	'ACP_UPDATER_EXT_TITLE_EXPLAIN'		=> 'O Atualizador do Upload Extensions permite que você verifique as atualizações do Upload Extensions e instale-as sem usar o FTP.',

	'EXT_UPLOAD_INIT_FAIL'				=> 'Houve um erro ao inicializar o processo de envio da extensão.',
	'EXT_NOT_WRITABLE'					=> 'O diretório ext/ não é gravável. Isso é necessário para que a "Upload Extensions" funcione corretamente. Por favor ajuste suas permissões ou configurações e tente novamente.',
	'EXT_UPLOAD_ERROR'					=> 'A extensão não foi enviada. Por favor, confirme que você fez o envio do arquivo zip da extensão real e tente novamente.',
	'EXT_TMP_NOT_WRITABLE'				=> 'O diretório ext/boardtools/updater/tmp/ não é gravável. Isso é necessário para que "Upload Extensions" funcione corretamente. Por favor ajuste suas permissões ou configurações e tente novamente.',
	'EXT_ALLOW_URL_FOPEN_DISABLED'		=> 'A configuração allow_url_fopen deve ser ativada para carregar informações de um recurso remoto. Por favor, confirme que a configuração allow_url_fopen está ativada no seu php.ini e tente novamente.',
	'EXT_OPENSSL_DISABLED'				=> 'A extensão openssl deve estar ativada para carregar informações de um recurso https. Por favor, confirme que a extensão openssl está ativada no seu php.ini e tente novamente.',
	'NO_UPLOAD_FILE'					=> 'Nenhum arquivo especificado ou ocorreu um erro durante o processo de envio.',
	'NOT_AN_EXTENSION'					=> 'O arquivo zip enviado não é uma extensão do phpBB. O arquivo não foi salvo no servidor.',

	'STATUS'							=> 'Status',
	'UPLOAD_EXTENSIONS'					=> 'Upload Extensions',
	'UPDATER_UPLOAD_NOT_INSTALLED'		=> 'O Upload Extensions não está instalado no seu fórum.',
	'UPDATER_UPLOAD_NO_NEW_UPDATES'		=> 'Novas atualizações não estão disponíveis no momento.',
	'UPDATER_UPLOAD_NEW_UPDATES'		=> 'Novas atualizações estão prontas para serem instaladas.',
	'UPDATER_UPLOAD_NO_UPDATES_INFO'	=> 'Informações sobre atualizações disponíveis não puderam ser obtidas.',

	'EXT_UPDATER_STEP_1'				=> '1. Verifique a disponibilidade do novo lançamento.',
	'EXT_UPDATER_STEP_2'				=> '2. Instale as atualizações.',
	'EXT_UPDATER_STEP_3'				=> '3. Ative a extensão.',

	'SOURCE'							=> 'Fonte',
	'EXTENSION_UPDATE'					=> 'Atualize a extensão',
	'EXTENSION_UPDATE_NO_LINK'			=> 'Link de download não é fornecido.',
	'EXTENSION_TO_BE_ENABLED'			=> 'O Upload Extensions será desativado durante o processo de atualização e ativado novamente após a atualização.',
	'EXT_ACTION_ERROR'					=> 'A ação solicitada não pode ser executada.',

	'ACP_UPLOAD_EXT_ERROR_DEST'			=> 'Nenhum fornecedor ou pasta de destino no arquivo zip enviado. O arquivo não foi salvo no servidor.',
	'ACP_UPLOAD_EXT_ERROR_COMP'			=> 'composer.json não foi encontrado no arquivo zip enviado. O arquivo não foi salvo no servidor.',
	'ACP_UPLOAD_EXT_ERROR_NOT_SAVED'	=> 'O arquivo não foi salvo no servidor.',
	'ACP_UPLOAD_EXT_WRONG_RESTORE'		=> 'Ocorreu um erro durante a atualização de uma extensão instalada. Tente atualizá-lo novamente.',
	'ACP_UPLOAD_EXT_NOT_COMPATIBLE'		=> 'Esta extensão permite que você atualize apenas o Upload Extensions. Parece que você tentou instalar uma atualização para outra extensão. Por favor, use o Upload Extensions para atualizar outras extensões.',

	'EXT_ENABLE'						=> 'Ativar',
	'EXT_ENABLED'						=> 'A extensão foi ativada com sucesso.',
	'EXT_ENABLED_LATEST'				=> 'A versão mais recente da extensão foi ativada com sucesso.',
	'EXT_UPLOADED'						=> 'O envio foi bem sucedido.',

	'ERROR_COPY_FILE'					=> 'A tentativa de copiar o arquivo “%1$s” para o local “%2$s” falhou.', 
	'ERROR_CREATE_DIRECTORY'			=> 'A tentativa de criar o diretório “%s” falhou.',
	'ERROR_REMOVE_DIRECTORY'			=> 'A tentativa de remover o diretório “%s” falhou.', 
));
