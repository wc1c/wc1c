<?php
/**
 * Namespace
 */
namespace Wc1c\Settings;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Abstracts\SettingsAbstract;

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