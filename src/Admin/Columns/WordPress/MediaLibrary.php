<?php namespace Wc1c\Admin\Columns\WordPress;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;

/**
 * MediaLibrary
 *
 * @package Wc1c\Admin
 */
final class MediaLibrary
{
	use SingletonTrait;

	/**
	 * MediaLibrary constructor.
	 */
	public function __construct()
	{
		add_filter('manage_media_columns',  [$this, 'manage_media_columns']);
		add_action('manage_media_custom_column', [$this, 'manage_media_custom_column']);
	}

	/**
	 * Adding a column to the list of media files for displaying 1C information
	 *
	 * @param $columns
	 *
	 * @return array
	 */
	public function manage_media_columns($columns)
	{
		$columns_after = array
		(
			'wc1c' => __('1C information', 'wc1c'),
		);

		return array_merge($columns, $columns_after);
	}

	/**
	 * Information from 1C
	 *
	 * @param $column
	 */
	public function manage_media_custom_column($column)
	{
		global $post;

		if('wc1c' === $column)
		{
			$schema_id = get_post_meta($post->ID, '_wc1c_schema_id', true);
			$config_id = get_post_meta($post->ID, '_wc1c_configuration_id', true);

			$content = '';

			if($schema_id)
			{
				$content .= '<span class="na">' . __('Schema ID: ', 'wc1c') . $schema_id . '</span>';
			}

			if($config_id)
			{
				$content .= '<br/><span class="na">' . __('Configuration ID: ', 'wc1c')  . $config_id . '</span>';
			}

			if(has_filter('wc1c_admin_interface_media_library_lists_column'))
			{
				$content = apply_filters('wc1c_admin_interface_media_library_lists_column', $content, $schema_id, $config_id, $post);
			}

			if('' === $content)
			{
				$content .= '<span class="na">' . __('Not found', 'wc1c') . '</span>';
			}

			echo $content;
		}
	}
}