<?php
/**
 * Admin configurations list class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_List
{
	/**
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Wc1c_Admin_Configurations_List constructor.
	 */
	public function __construct()
	{
		add_action('wc1c_admin_configurations_list_show', [$this, 'list_table'], 10);
		add_action('wc1c_admin_configurations_show', [$this, 'template_output'], 10);
	}

	/**
	 * Template output
	 */
	public function template_output()
	{
		wc1c_get_template('configurations/list.php');
	}

	/**
	 * Build table
	 */
	public function list_table()
	{
		$list_table = new Wc1c_Admin_Configurations_List_Table();
		$list_table->prepare_items();

		$list_table->display();
	}
}