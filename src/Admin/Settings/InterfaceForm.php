<?php namespace Wc1c\Admin\Settings;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
use Wc1c\Settings\InterfaceSettings;

/**
 *  InterfaceForm
 *
 * @package Wc1c\Admin
 */
class InterfaceForm extends Form
{
	/**
	 * InterfaceForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->set_id('settings-interface');
		$this->setSettings(new InterfaceSettings());

		add_filter('wc1c_' . $this->get_id() . '_form_load_fields', [$this, 'init_fields_interface'], 10);

		$this->init();
	}
	/**
	 * Add for Interface
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_interface($fields)
	{
		$fields['admin_interface'] =
		[
			'title' => __('Changing the interface', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Allow changes to WordPress and WooCommerce dashboard interface?', 'wc1c'),
			'description' => __('If enabled, new features will appear in the WordPress and WooCommerce interface according to the interface change settings.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['interface_title'] =
		[
			'title' => __('WooCommerce', 'wc1c'),
			'type' => 'title',
			'description' => __('Some interface settings for the WooCommerce.', 'wc1c'),
		];

		$fields['admin_interface_products_column'] =
		[
			'title' => __('Column in products list', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a column with information from 1C to the list of products.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['admin_interface_products_edit_metabox'] =
		[
			'title' => __('Metabox in edit products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a Metabox with information from 1C in edit products.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['admin_interface_orders_column'] =
		[
			'title' => __('Column in orders list', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a column with information from 1C to the list of orders.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['admin_interface_orders_edit_metabox'] =
		[
			'title' => __('Metabox in edit orders', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a Metabox with information from 1C in edit orders.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['admin_interface_categories_column'] =
		[
			'title' => __('Column in categories list', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a column with information from 1C to the list of categories.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['admin_interface_media_library_column'] =
		[
			'title' => __('Column in media library list', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a column with information from 1C to the list of media files.', 'wc1c'),
			'default' => 'yes'
		];

		return $fields;
	}
}