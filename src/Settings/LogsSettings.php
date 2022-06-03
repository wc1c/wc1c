<?php namespace Wc1c\Settings;

defined('ABSPATH') || exit;

use Wc1c\Settings\Abstracts\SettingsAbstract;

/**
 * LogsSettings
 *
 * @package Wc1c\Settings
 */
class LogsSettings extends SettingsAbstract
{
	/**
	 * LogsSettings constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('logs');
	}
}