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
			$this->init_actions();
		}
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