<?php namespace Wc1c\Settings;

defined('ABSPATH') || exit;

use Wc1c\Settings\Abstracts\SettingsAbstract;

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
}