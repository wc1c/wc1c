<?php namespace Wc1c\Settings;

defined('ABSPATH') || exit;

use Wc1c\Abstracts\SettingsAbstract;

/**
 * ConnectionSettings
 *
 * @package Wc1c\Settings
 */
class ConnectionSettings extends SettingsAbstract
{
	/**
	 * ConnectionSettings constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('connection');
	}

	/**
	 * Account connected?
	 *
	 * @return bool
	 */
	public function isConnected()
	{
		if($this->get('token', '') !== '')
		{
			return true;// todo: проверять подключение раз в n времени с сохранением в транзиты
		}

		return false;
	}
}