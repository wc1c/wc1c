<?php namespace Wc1c\Settings;

defined('ABSPATH') || exit;

use Wc1c\Abstracts\SettingsAbstract;

/**
 * InterfaceSettings
 *
 * @package Wc1c\Settings
 */
class InterfaceSettings extends SettingsAbstract
{
	/**
	 * InterfaceSettings constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('interface');
	}
}