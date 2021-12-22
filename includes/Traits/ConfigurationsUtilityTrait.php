<?php namespace Wc1c\Traits;

defined('ABSPATH') || exit;

/**
 * ConfigurationsUtilityTrait
 *
 * @package Wc1c\Traits
 */
trait ConfigurationsUtilityTrait
{
	/**
	 * Get all available configurations statuses
	 *
	 * @return array
	 */
	public function utilityConfigurationsGetStatuses()
	{
		$statuses =
		[
			'draft',
			'inactive',
			'active',
			'processing',
			'error',
			'deleted',
		];

		return apply_filters(WC1C_PREFIX . 'configurations_get_statuses', $statuses);
	}

	/**
	 * Get normal configuration status
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public function utilityConfigurationsGetStatusesLabel($status)
	{
		$default_label = __('Undefined', 'wc1c');

		$statuses_labels = apply_filters
		(
			WC1C_PREFIX . 'configurations_get_statuses_labels',
			[
				'draft' => __('Draft', 'wc1c'),
				'active' => __('Active', 'wc1c'),
				'inactive' => __('Inactive', 'wc1c'),
				'error' => __('Error', 'wc1c'),
				'processing' => __('Processing', 'wc1c'),
				'deleted' => __('Deleted', 'wc1c'),
			]
		);

		if(empty($status) || !array_key_exists($status, $statuses_labels))
		{
			$status_label = $default_label;
		}
		else
		{
			$status_label = $statuses_labels[$status];
		}

		return apply_filters(WC1C_PREFIX . 'configurations_get_statuses_label_return', $status_label, $status, $statuses_labels);
	}

	/**
	 * Get folder name for configuration statuses
	 *
	 * @param string $status
	 *
	 * @return string
	 */
	public function utilityConfigurationsGetStatusesFolder($status)
	{
		$default_folder = __('Undefined', 'wc1c');

		$statuses_folders = apply_filters
		(
			WC1C_PREFIX . 'configurations_get_statuses_folders',
			[
				'draft' => __('Drafts', 'wc1c'),
				'active' => __('Activated', 'wc1c'),
				'inactive' => __('Deactivated', 'wc1c'),
				'error' => __('With errors', 'wc1c'),
				'processing' => __('In processing', 'wc1c'),
				'deleted' => __('Trash', 'wc1c'),
			]
		);

		$status_folder = $default_folder;

		if(!empty($status) || array_key_exists($status, $statuses_folders))
		{
			$status_folder = $statuses_folders[$status];
		}

		return apply_filters(WC1C_PREFIX . 'configurations_get_statuses_folder_return', $status_folder, $status, $statuses_folders);
	}
}