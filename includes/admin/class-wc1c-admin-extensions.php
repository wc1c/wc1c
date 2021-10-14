<?php
/**
 * Extensions class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Extensions
{
	/**
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Wc1c_Admin_Extensions constructor.
	 */
	public function __construct()
	{
		add_action('wc1c_admin_extensions_show', [$this, 'output'], 10);
	}

	/**
	 * Extensions
	 *
	 * @return void
	 */
	public function page_extensions()
	{
		wc1c_get_template('page.php');
	}

	/**
	 * Output tools table
	 *
	 * @return void
	 */
	public function output()
	{
		$extensions = WC1C()->get_extensions();

		if(empty($extensions))
		{
			wc1c_get_template('extensions/404.php');
			return;
		}

		foreach($extensions as $extension_id => $extension_object)
		{
			$args =
			[
				'id' => $extension_id,
				'object' => $extension_object
			];

			wc1c_get_template('extensions/item.php', $args);
		}
	}
}