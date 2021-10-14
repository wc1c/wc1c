<?php
/**
 * Default tool class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Tool_Environments extends Abstract_Wc1c_Tool
{
	/**
	 * Wc1c_Tool_Environments constructor
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialize
	 *
	 * @return bool
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