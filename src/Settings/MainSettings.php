<?php namespace Wc1c\Settings;

defined('ABSPATH') || exit;

use Wc1c\Settings\Abstracts\SettingsAbstract;

/**
 * Class MainSettings
 *
 * @package Wc1c\Settings
 */
class MainSettings extends SettingsAbstract
{
	/**
	 * Main constructor.
	 */
	public function __construct()
	{
		$this->setOptionName('main');
	}
}