<?php
/**
 * Tools class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Tools
{
	/**
	 * Wc1c_Admin_Tools constructor
	 *
	 * @param bool $init
	 */
	public function __construct($init = true)
	{
		/**
		 * Auto init
		 */
		if($init)
		{
			$this->init();
		}
	}

	/**
	 * Initialized
	 */
	public function init()
	{
		/**
		 * Output tools table
		 */
		add_action('wc1c_admin_tools_show', array($this, 'output'), 10);
	}

	/**
	 * Output tools table
	 *
	 * @return void
	 */
	public function output()
	{
		$tools = WC1C()->get_tools();

		if(empty($tools))
		{
			wc1c_get_template('tools/404.php');
			return;
		}

		$tool_id = wc1c_get_var($_GET['tool_id'], '');

		wc1c_get_template('tools/header.php');

		if($tool_id !== '' && is_wc1c_admin_tools_request())
		{
			$args =
				[
					'id' => $tool_id,
					'object' => $tools[$tool_id]
				];

			wc1c_get_template('tools/item.php', $args);
		}
		else
		{
			foreach($tools as $tool_id => $tool_object)
			{
				$args =
					[
						'id' => $tool_id,
						'object' => $tool_object
					];

				wc1c_get_template('tools/item.php', $args);
			}
		}
	}
}