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
	 * Wc1c_Admin_Configurations_List constructor.
	 *
	 * @param bool $init
	 */
	public function __construct($init = true)
	{
		if($init)
		{
			$this->includes();
			$this->init_actions();
		}
	}

	/**
	 * Include required files
	 */
	public function includes()
	{
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-admin-table.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-list-table.php';
	}

	/**
	 * Initialized
	 */
	public function init_actions()
	{
		add_action('wc1c_admin_configurations_list_show', array($this, 'configurations_list_table'), 10);
		add_action('wc1c_admin_configurations_show', array($this, 'template_output'), 10);
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
	public function configurations_list_table()
	{
		$list_table = new Wc1c_Admin_Configurations_List_Table();
		$list_table->prepare_items();
		$list_table->display();
	}
}