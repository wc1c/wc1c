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
				break;
			case 'update':
				new Wc1c_Admin_Configurations_Update();
				break;
			case 'remove':
				new Wc1c_Admin_Configurations_Remove();
				break;
			case 'list':
				new Wc1c_Admin_Configurations_List();
				break;
			default:
				wc1c_get_template('configurations/404.php');
		}
	}

	/**
	 * Include required files
	 */
	public function includes()
	{
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-list.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-create.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-remove.php';
		include_once WC1C_PLUGIN_PATH . 'includes/admin/configurations/class-wc1c-admin-configurations-update.php';
	}

	/**
	 * Current action detect
	 */
	public function init_current_action()
	{
		$do_action = wc1c_get_var($_GET['do_action'], 'list');

		if(in_array($do_action, $this->get_actions(), true))
		{
			$this->set_current_action($do_action);
		}
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
}