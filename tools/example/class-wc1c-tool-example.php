<?php
/**
 * Default tool class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Tool_Example extends Abstract_Wc1c_Tool
{
	/**
	 * Wc1c_Tool_Example constructor
	 */
	public function __construct()
	{
		if(!is_wc1c_admin_tools_request('example'))
		{
			return;
		}

		/**
		 * Init
		 */
		$this->init();
	}

	/**
	 * Initialize
	 */
	public function init()
	{
		add_action('wc1c_admin_tools_single_show', [$this, 'output']);
	}

	/**
	 * Show on page
	 */
	public function output()
	{
		echo 'Example content';
	}
}