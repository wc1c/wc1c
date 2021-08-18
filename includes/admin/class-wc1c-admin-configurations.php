<?php
/**
 * Configuration class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations
{
	/**
	 * Current action
	 *
	 * @var string
	 */
	private $current_action = 'list';

	/**
	 * Available actions
	 *
	 * @var array
	 */
	private $actions = array
	(
		'list',
		'create',
		'update',
		'remove'
	);

	/**
	 * Wc1c_Admin_Configurations constructor
	 */
	public function __construct()
	{
		$this->includes();
		$this->init_current_action();

		$current_action = $this->get_current_action();

		switch ($current_action)
		{
			case 'create':
				new Wc1c_Admin_Configurations_Create();
				add_action('wc1c_admin_configurations_show', array($this, 'configurations_create'), 10);
				break;
			case 'update':
				new Wc1c_Admin_Configurations_Update();
				add_action('wc1c_admin_configurations_show', array($this, 'configurations_update'), 10);
				break;
			default:
				add_action('wc1c_admin_configurations_list_show', array($this, 'configurations_list_table'), 10);
				add_action('wc1c_admin_configurations_show', array($this, 'configurations_list'), 10);
		}
	}

	/**
	 * Current action detect
	 */
	public function init_current_action()
	{
		$do_action = wc1c_get_var($_GET['do_action'], 'list');

		if(in_array($do_action, $this->get_actions()))
		{
			$this->set_current_action($do_action);
		}
	}

	/**
	 * Include required files
	 */
	public function includes()
	{
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-admin-table.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-table.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-create.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-update.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-update-form.php';
	}

	/**
	 * Get actions
	 *
	 * @return array
	 */
	public function get_actions()
	{
		return $this->actions;
	}

	/**
	 * Set actions
	 *
	 * @param array $actions
	 */
	public function set_actions($actions)
	{
		$this->actions = $actions;
	}

	/**
	 * Get current action
	 *
	 * @return string
	 */
	public function get_current_action()
	{
		return $this->current_action;
	}

	/**
	 * Set current action
	 *
	 * @param string $current_action
	 */
	public function set_current_action($current_action)
	{
		$this->current_action = $current_action;
	}

	/**
	 * Build table
	 */
	public function configurations_list_table()
	{
		$list_table = new Wc1c_Admin_Configurations_Table();
		$list_table->prepare_items();
		$list_table->display();
	}

	/**
	 * List configurations
	 */
	public function configurations_list()
	{
		wc1c_get_template('configurations/list.php');
	}

	/**
	 * Add configurations
	 */
	public function configurations_create()
	{
		wc1c_get_template('configurations/create.php');
	}

	/**
	 * Update configurations
	 */
	public function configurations_update()
	{
		wc1c_get_template('configurations/update.php');
	}
}