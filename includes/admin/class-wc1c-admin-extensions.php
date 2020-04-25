<?php
/**
 * Extensions class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Extensions
{
	/**
	 * Data
	 *
	 * @var array
	 */
	private $extensions = array();

	/**
	 * Wc1c_Admin_Extensions constructor
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
		 * Output table
		 */
		add_action('wc1c_admin_extensions_show', array($this, 'output'), 10);
	}

	/**
	 * @return array
	 */
	public function get_extensions()
	{
		return $this->extensions;
	}

	/**
	 * @param array $extensions
	 */
	public function set_extensions($extensions)
	{
		$this->extensions = $extensions;
	}

	/**
	 * Loading tools
	 *
	 * @return array
	 */
	public function load_extensions()
	{
		/**
		 * Data for all extensions
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
		$extensions = array();

		/**
		 * Build new data
		 */
		foreach(WC1C()->get_extensions() as $raw_data_key => $raw_data_value)
		{
			$extensions[$raw_data_key]['id'] = $raw_data_key;
			$extensions[$raw_data_key]['name'] = $raw_data_value['name'];
			$extensions[$raw_data_key]['description'] = $raw_data_value['description'];
			$extensions[$raw_data_key]['author_name'] = $raw_data_value['author_name'];
			$extensions[$raw_data_key]['version'] = $raw_data_value['version'];
			$extensions[$raw_data_key]['version_min'] = $raw_data_value['wc1c_version_min'];
			$extensions[$raw_data_key]['version_max'] = $raw_data_value['wc1c_version_max'];
			$extensions[$raw_data_key]['version_php_min'] = $raw_data_value['version_php_min'];
			$extensions[$raw_data_key]['version_php_max'] = $raw_data_value['version_php_max'];
		}

		$this->set_extensions($extensions);

		return $this->get_extensions();
	}

	/**
	 * Output tools table
	 *
	 * @return void
	 */
	public function output()
	{
		/**
		 * Load all extensions
		 */
		$data = $this->load_extensions();

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