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
		add_filter('manage_edit-product_columns',  array($this, 'manage_edit_product_columns'));
		add_action('manage_product_posts_custom_column', array($this, 'manage_product_posts_custom_column'));
	}

	/**
	 * Добавление колонки в список постов для вывода UID 1С
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
	 * Вывод идентификаторов 1С в колонку с постами
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

			$guid = false;

			if($config_id && $schema_id)
			{
				$guid = get_post_meta($post->ID, 'wc1c_prefix_' . $schema_id . '_' . $config_id . '_id80', true);

				echo '<span class="na">' . __('Schema ID: ', 'wc1c') . $schema_id . '</span><br/>';
			}

			echo $guid ? $guid : '<span class="na">' . __('not found', 'wc1c') . '</span>';

			if($config_id)
			{
				echo '<br/><span class="na">' . __('Configuration ID: ', 'wc1c')  . $config_id . '</span>';
			}
		}
	}
}