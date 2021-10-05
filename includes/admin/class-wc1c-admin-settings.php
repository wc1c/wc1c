<?php
/**
 * Settings class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Settings
{
	/**
	 * Wc1c_Admin_Settings constructor
	 */
	public function __construct()
	{
		add_filter('wc1c_admin_settings_form_load_saved_data', [$this, 'load_data'], 10, 1);

		$form = new Wc1c_Admin_Settings_Form();
		$form->init();

		add_action('wc1c_admin_settings_show', [$form, 'output_form'], 10);
	}

	public function load_data($old_data)
	{
		try
		{
			$saved_data = WC1C()->settings()->get();

			$old_data = array_merge($old_data, $saved_data);
		}
		catch(Exception $e){}

		return $old_data;
	}
}