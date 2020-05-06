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
	 * Data for schemas
	 *
	 * @var array
	 */
	private $schemas = array();

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
	 * @return array
	 */
	public function get_schemas()
	{
		return $this->schemas;
	}

	/**
	 * @param array $schemas
	 */
	public function set_schemas($schemas)
	{
		$this->schemas = $schemas;
	}

	/**
	 * Loading schemas
	 *
	 * @return array
	 */
	public function load_schemas()
	{
		/**
		 * Data for all schemas
		 * [key] - schema unique id
		 *  [name] - schema title
		 *  [description] - schema description, optional
		 *  [author_name] - schema author name
		 *  [version] - current schema version
		 *  [version_min] - minimal WC1C support version
		 *  [version_max] - maximal WC1C support version
		 *  [version_php_min] - minimal PHP support version
		 *  [version_php_max] - maximal PHP support version
		 */
		$schemas = array();

		/**
		 * Build new data
		 */
		foreach(WC1C()->get_schemas() as $raw_data_key => $raw_data_value)
		{
			$schemas[$raw_data_key]['id'] = $raw_data_key;
			$schemas[$raw_data_key]['name'] = $raw_data_value['name'];
			$schemas[$raw_data_key]['description'] = $raw_data_value['description'];
			$schemas[$raw_data_key]['author_name'] = $raw_data_value['author_name'];
			$schemas[$raw_data_key]['version'] = $raw_data_value['version'];
			$schemas[$raw_data_key]['version_min'] = $raw_data_value['version_min'];
			$schemas[$raw_data_key]['version_max'] = $raw_data_value['version_max'];
			$schemas[$raw_data_key]['version_php_min'] = $raw_data_value['version_php_min'];
			$schemas[$raw_data_key]['version_php_max'] = $raw_data_value['version_php_max'];
		}

		/**
		 * Set data
		 */
		$this->set_schemas($schemas);

		/**
		 * Return data
		 */
		return $this->get_schemas();
	}

	/**
	 * Output schemas table
	 *
	 * @return void
	 */
	public function output()
	{
		$data = $this->load_schemas();

		/**
		 * Print head description before tables
		 */
		echo '<p>' . __('Information about schemas installed in the system. Configurations are created based on established schemas.', 'wc1c') . '</p>';
		echo '<h2>' . __('Available schemas', 'wc1c') . '</h2>';

		/**
		 * Show data
		 */
		foreach($data as $data_key => $data_value)
		{
			/**
			 * Open table & header
			 */
			echo '<table class="wp-list-table widefat striped" style="margin-bottom: 10px;"><thead><tr>
            <th colspan="2"><h4 style="margin: 0.2em 0;">' . esc_html__($data_value['name']) . '</h4></th>
        </tr></thead><tbody>';
			echo '<tr>';
			echo '<td style="width: 35%;">';

			/**
			 * One column
			 */
			echo __('Identification (ID): ', 'wc1c') . esc_html__($data_value['id']) . '<br />';
			echo __('Version: ', 'wc1c') . esc_html__($data_value['version']) . '<br />';
			echo __('Author: ', 'wc1c') . esc_html__($data_value['author_name']) . '<br />';
			echo __('PHP minimal version: ', 'wc1c') . esc_html__($data_value['version_php_min']) . '<br />';
			echo __('PHP maximal version: ', 'wc1c') . esc_html__($data_value['version_php_max']) . '<br />';
			echo __('WC1C minimal version: ', 'wc1c') . esc_html__($data_value['version_min']) . '<br />';
			echo __('WC1C maximal version: ', 'wc1c') . esc_html__($data_value['version_max']) . '<br />';

			echo '</td>';

			/**
			 * Two column
			 */
			echo '<td style="background-color: #ffffff;">' . esc_html__(apply_filters('wc1c_admin_schemas_description_row_print', $data_value['description'], $data_key)) . '<br />';

			/**
			 * Close table
			 */
			echo '</td></tr>';
			echo '</tbody></table>';
		}
	}
}