<?php
/**
 * Admin inject class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Inject
{
	/**
	 * Wc1c_Admin_Inject constructor
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialization
	 */
	public function init()
	{
		$this->hooks();
	}

	/**
	 * Hook into actions and filters
	 */
	private function hooks()
	{
		if(WC1C()->settings()->get('admin_inject', 'yes') !== 'yes')
		{
			return;
		}

		if(WC1C()->settings()->get('admin_inject', 'yes') === 'yes')
		{
			add_filter('manage_edit-product_columns',  array($this, 'manage_edit_product_columns'));
			add_action('manage_product_posts_custom_column', array($this, 'manage_product_posts_custom_column'));
		}
	}

	/**
	 * Adding a column to the list of products for displaying 1C information
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function manage_edit_product_columns($columns)
	{
		$columns_after = array
		(
			'wc1c' => __('1C information', 'wc1c'),
		);

		return array_merge($columns, $columns_after);
	}

	/**
	 * Information from 1C in products list
	 *
	 * @param $column
	 */
	public function manage_product_posts_custom_column($column)
	{
		global $post;

		if($column === 'wc1c')
		{
			$schema_id = get_post_meta($post->ID, 'wc1c_schema_id', true);
			$config_id = get_post_meta($post->ID, 'wc1c_configuration_id', true);

			$content = '';

			if($schema_id)
			{
				$content .= '<span class="na">' . __('Schema ID: ', 'wc1c') . $schema_id . '</span>';
			}

			if($config_id)
			{
				$content .= '<br/><span class="na">' . __('Configuration ID: ', 'wc1c')  . $config_id . '</span>';
			}

			if($config_id == false && $schema_id == false)
			{
				$content .= '<span class="na">' . __('not found', 'wc1c') . '</span>';
			}

			$content = apply_filters('wc1c_admin_inject_products_lists_column', $content, $schema_id, $config_id);

			echo $content;
		}
	}
}