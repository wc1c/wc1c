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
		if(false !== get_option('wc1c_wizard_setup', false))
		{
			SetupWizard::instance();
		}

		/**
		 * Update
		 */
		if(false !== get_option('wc1c_wizard_update', false))
		{
			UpdateWizard::instance();
		}
	}
}