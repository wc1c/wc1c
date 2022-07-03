<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;

/**
 * Activation
 *
 * @package Wc1c
 */
final class Activation
{
	use SingletonTrait;

	public function __construct()
	{
		if(false === get_option('wc1c_version', false))
		{
			update_option('wc1c_wizard', 'setup');

			wc1c()->admin()->notices()->create
			(
				[
					'id' => 'activation_welcome',
					'dismissible' => false,
					'type' => 'info',
					'data' => __('WC1C successfully activated. You have made the right choice to integrate the site with 1C (plugin number one)!', 'wc1c'),
					'extra_data' => sprintf
					(
						'<p>%s <a href="%s">%s</a></p>',
						__('The basic plugin setup has not been done yet, so you can proceed to the setup, which takes no more than 5 minutes.', 'wc1c'),
						admin_url('admin.php?page=wc1c'),
						__('Go to setting.', 'wc1c')
					)
				]
			);
		}

		if(false === get_option('wc1c_version_init', false))
		{
			update_option('wc1c_version_init', wc1c()->environment()->get('wc1c_version'));
		}
	}
}