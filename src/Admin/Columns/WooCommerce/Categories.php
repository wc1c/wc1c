<?php namespace Wc1c\Admin\Columns\WooCommerce;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;
use Wc1c\Wc\Category;

/**
 * Categories
 *
 * @package Wc1c\Admin\Columns\WooCommerce
 */
final class Categories
{
	use SingletonTrait;

	/**
	 * Categories constructor.
	 */
	public function __construct()
	{
		add_filter('manage_edit-product_cat_columns', [$this, 'manage_edit_taxonomy_columns']);
		add_filter('manage_product_cat_custom_column', [$this, 'manage_taxonomy_custom_column'], 10, 3);
	}

	/**
	 * Adding a column to the categories for displaying 1C information
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function manage_edit_taxonomy_columns($columns)
	{
		$columns_after =
		[
			'wc1c' => __('1C information', 'wc1c'),
		];

		return array_merge($columns, $columns_after);
	}

	/**
	 * Information from 1C in categories list
	 *
	 * @param $columns
	 * @param $column
	 * @param $id
	 *
	 * @return string
	 */
	public function manage_taxonomy_custom_column($columns, $column, $id)
	{
		if('wc1c' === $column)
		{
			$content = '';

			$content = apply_filters('wc1c_admin_interface_categories_column', $content, $columns, $id);

			if($content === '')
			{
				$content = '<span class="na">' . __('not found', 'wc1c') . '</span>';
			}

			$columns .= $content;
		}

		return $columns;
	}
}