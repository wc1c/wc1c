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
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Available actions
	 *
	 * @var array
	 */
	private $actions =
	[
		'list',
		'create',
		'update',
		'remove'
	];

	/**
	 * Current action
	 *
	 * @var string
	 */
	private $current_action = 'list';

	/**
	 * Wc1c_Admin_Configurations constructor
	 */
	public function __construct()
	{
		$actions = apply_filters('wc1c_admin_configurations_init_actions', $this->actions);

		$this->set_actions($actions);

		$current_action = $this->init_current_action();

		switch($current_action)
		{
			case 'create':
				Wc1c_Admin_Configurations_Create::instance();
				break;
			case 'update':
				Wc1c_Admin_Configurations_Update::instance();
				break;
			case 'remove':
				Wc1c_Admin_Configurations_Remove::instance();
				break;
			default:
				Wc1c_Admin_Configurations_List::instance();
		}

		add_action('wc1c_admin_body_show', [$this, 'output'], 10);
	}

	/**
	 * Detect current action
	 *
	 * @return string
	 */
	public function init_current_action()
	{
		$do_action = wc1c_get_var($_GET['do_action'], 'list');

		if(in_array($do_action, $this->get_actions(), true))
		{
			$this->set_current_action($do_action);
		}

		return $this->get_current_action();
	}

	/**
	 * Get actions
	 *
	 * @return array
	 */
	public function get_actions()
	{
		return apply_filters('wc1c_admin_configurations_get_actions', $this->actions);
	}

	/**
	 * Set actions
	 *
	 * @param array $actions
	 */
	public function set_actions($actions)
	{
		// hook
		$actions = apply_filters('wc1c_admin_configurations_set_actions', $actions);

		$this->actions = $actions;
	}

	/**
	 * Get current action
	 *
	 * @return string
	 */
	public function get_current_action()
	{
		return apply_filters('wc1c_admin_configurations_get_current_action', $this->current_action);
	}

	/**
	 * Set current action
	 *
	 * @param string $current_action
	 */
	public function set_current_action($current_action)
	{
		// hook
		$current_action = apply_filters('wc1c_admin_configurations_set_current_action', $current_action);

		$this->current_action = $current_action;
	}

	/**
	 * Output page
	 */
	public function output()
	{
		wc1c_get_template('configurations/page.php');
	}
}