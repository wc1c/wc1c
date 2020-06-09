<?php
/**
 * Default tool class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Tool_Example extends Wc1c_Abstract_Tool
{
	/**
	 * Wc1c_Tool_Example constructor
	 */
	public function __construct()
	{
		/**
		 * Init
		 */
		$this->init();
	}

	/**
	 * Initialize
	 *
	 * @return bool|void
	 */
	public function init()
	{
		if(!is_wc1c_admin_tools_request('example'))
		{
			return false;
		}

		add_action('wc1c_admin_tools_single_show', [$this, 'show']);
	}

	/**
	 * Show on page
	 */
	public function show()
	{
		echo 'Example content';
	}
}