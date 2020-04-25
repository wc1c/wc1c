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
	 * Data for tools
	 *
	 * @var array
	 */
	private $tools = array();

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
	 * @return array
	 */
	public function get_tools()
	{
		return $this->tools;
	}

	/**
	 * @param array $tools
	 */
	public function set_tools($tools)
	{
		$this->tools = $tools;
	}

	/**
	 * Loading tools
	 *
	 * @return array
	 */
	public function load_tools()
	{
		/**
		 * Data for all tools
		 * [key] - tool unique id
		 *  [name] - tool title
		 *  [description] - tool description, optional
		 *  [author_name] - tool author name
		 *  [version] - current tool version
		 *  [version_min] - minimal WC1C support version
		 *  [version_max] - maximal WC1C support version
		 *  [version_php_min] - minimal PHP support version
		 *  [version_php_max] - maximal PHP support version
		 */
		$tools = array();

		/**
		 * Build new data
		 */
		foreach(WC1C()->get_tools() as $raw_data_key => $raw_data_value)
		{
			$tools[$raw_data_key]['id'] = $raw_data_key;
			$tools[$raw_data_key]['name'] = $raw_data_value['name'];
			$tools[$raw_data_key]['description'] = $raw_data_value['description'];
			$tools[$raw_data_key]['author_name'] = $raw_data_value['author_name'];
			$tools[$raw_data_key]['version'] = $raw_data_value['version'];
			$tools[$raw_data_key]['version_min'] = $raw_data_value['wc1c_version_min'];
			$tools[$raw_data_key]['version_max'] = $raw_data_value['wc1c_version_max'];
			$tools[$raw_data_key]['version_php_min'] = $raw_data_value['version_php_min'];
			$tools[$raw_data_key]['version_php_max'] = $raw_data_value['version_php_max'];
		}

		/**
		 * Set data
		 */
		$this->set_tools($tools);

		/**
		 * Return data
		 */
		return $this->get_tools();
	}

	/**
	 * Output tools table
	 *
	 * @return void
	 */
	public function output()
	{
		/**
		 * Load all schemas
		 */
		$data = $this->load_tools();

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
			echo '<td style="background-color: #ffffff;">' . esc_html__(apply_filters('wc1c_admin_tools_description_show', $data_value['description'], $data_key)) . '<br />';

			/**
			 * Close table
			 */
			echo '</td></tr>';
			echo '</tbody></table>';
		}
	}
}