<?php
/**
 * Schemas class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Schemas
{
	/**
	 * Wc1c_Admin_Schemas constructor
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
		 * Output schemas table
		 */
		add_action('wc1c_admin_schemas_show', array($this, 'output'), 10);
	}

	/**
	 * Output schemas table
	 *
	 * @return void
	 */
	public function output()
	{
		$schemas = WC1C()->get_schemas();

		if(empty($schemas))
		{
			wc1c_get_template('schemas_404.php');
			return;
		}

		wc1c_get_template('schemas_header.php');

		foreach($schemas as $schema_id => $schema_object)
		{
			$args =
				[
					'id' => $schema_id,
					'object' => $schema_object
				];

			wc1c_get_template('schemas_item.php', $args);
		}
	}
}