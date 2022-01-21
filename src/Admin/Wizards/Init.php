<?php namespace Wc1c\Admin\Wizards;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;

/**
 * Init
 *
 * @package Wc1c\Admin\Wizards
 */
final class Init
{
	use SingletonTrait;

	/**
	 * Init constructor.
	 */
	public function __construct()
	{
		/**
		 * Setup
		 */
		if('setup' === get_option('wc1c_wizard', false))
		{
			SetupWizard::instance();
		}

		/**
		 * Update
		 */
		if('update' === get_option('wc1c_wizard', false))
		{
			UpdateWizard::instance();
		}
	}
}