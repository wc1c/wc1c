<?php namespace Wc1c\Schemas\Flamixcml;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;
use Wc1c\Traits\UtilityTrait;

/**
 * Admin
 *
 * @package Wc1c\Schemas\Flamixcml
 */
class Admin
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * @var Core Schema core
	 */
	protected $core;

	/**
	 * @return Core
	 */
	public function core()
	{
		return $this->core;
	}

	/**
	 * @param Core $core
	 */
	public function setCore($core)
	{
		$this->core = $core;
	}

	/**
	 * @return void
	 */
	public function initConfigurationsFields()
	{
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsReceiver'], 10, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsCatalog'], 40, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsInventories'], 70, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsProductsPrices'], 90, 1);

		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsLogs'], 100, 1);
		add_filter('wc1c_configurations-update_form_load_fields', [$this, 'configurationsFieldsOther'], 110, 1);
	}

	/**
	 * Configurations fields: receiver
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsReceiver($fields)
	{
		$fields['title_receiver'] =
		[
			'title' => __('Receiving requests', 'wc1c'),
			'type' => 'title',
			'description' => __('Authorization of requests and regulation of algorithms for receiving requests for the Receiver from the Flamix programs by CommerceML protocol.', 'wc1c'),
		];

		$lazy_sign = $this->core()->configuration()->getMeta('receiver_lazy_sign');

		if(empty($lazy_sign))
		{
			$lazy_sign = md5($this->core()->configuration()->getId() . time());
			$this->core()->configuration()->addMetaData('receiver_lazy_sign', $lazy_sign, true);
			$this->core()->configuration()->saveMetaData();
		}

		$url_raw = get_site_url(null, '/?wc1c-receiver=' . $this->core()->configuration()->getId() . '&lazysign=' . $lazy_sign . '&get_param');
		$url_raw = '<p class="input-text p-2 bg-light regular-input wc1c_urls">' . esc_html($url_raw) . '</p>';

		$fields['url_requests'] =
		[
			'title' => __('Website address', 'wc1c'),
			'type' => 'raw',
			'raw' => $url_raw,
			'description' => __('Specified in the exchange settings on the 1C side. The Recipient is located at this address, which will receive requests from 1C. When copying, you need to get rid of whitespace characters, if they are present.', 'wc1c'),
		];

		$fields['user_login'] =
		[
			'title' => __('Username', 'wc1c'),
			'type' => 'text',
			'description' => __('Specified in 1C when setting up an exchange with a site on the 1C side. At the same time, work with data on the site is performed on behalf of the configuration owner, and not on behalf of the specified username.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		];

		$fields['user_password'] =
		[
			'title' => __('User password', 'wc1c'),
			'type' => 'password',
			'description' => __('Specified in 1C paired with a username when setting up an exchange with a site on the 1C side. It is advisable not to specify the password for the current WordPress user.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: other
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsOther($fields)
	{
		$fields['title_other'] =
		[
			'title' => __('Other parameters', 'wc1c'),
			'type' => 'title',
			'description' => __('Change of data processing behavior for environment compatibility and so on.', 'wc1c'),
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum size of accepted requests', 'wc1c'),
			'type' => 'text',
			'description' => sprintf
			(
				'%s<br />%s <b>%s</b><br />%s',
				__('Enter the maximum size of accepted requests from 1C at a time in bytes. May be specified with a dimension suffix, such as 7M, where M = megabyte, K = kilobyte, G - gigabyte.', 'wc1c'),
				__('Current WC1C limit:', 'wc1c'),
				wc1c()->settings()->get('php_post_max_size', wc1c()->environment()->get('php_post_max_size')),
				__('Can only decrease the value, because it must not exceed the limits from the WC1C settings.', 'wc1c')
			),
			'default' => wc1c()->settings()->get('php_post_max_size', wc1c()->environment()->get('php_post_max_size')),
			'css' => 'min-width: 100px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products prices
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsPrices($fields)
	{
		$fields['title_products_prices'] =
		[
			'title' => __('Prices', 'wc1c'),
			'type' => 'title',
			'description' => __('Comprehensive settings for updating prices.', 'wc1c'),
		];

		$products_prices_by_cml_options =
		[
			'no' => __('Do not use', 'wc1c'),
			'yes_primary' => __('From first found', 'wc1c'),
			'yes_name' => __('From specified name', 'wc1c'),
		];

		$fields['products_prices_regular_by_cml'] =
		[
			'title' => __('Prices based on CommerceML data: regular', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods). The found price after use will not be available for selection as a sale price.', 'wc1c'),
				__('Do not use', 'wc1c'),
				__('Populating the regular prices data from CommerceML data will be skipped.', 'wc1c'),
				__('From first found', 'wc1c'),
				__('The first available price of all available prices for the product will be used as the regular price.', 'wc1c'),
				__('From specified name', 'wc1c'),
				__('The price with the specified name will be used as the regular price. If the price is not found by name, no value will be assigned.', 'wc1c')
			),
			'default' => 'no',
			'options' => $products_prices_by_cml_options
		];

		$fields['products_prices_regular_by_cml_from_name'] =
		[
			'title' => __('Prices based on CommerceML data: regular - name in 1C', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the name of the base price in 1C, which is used for filling to WooCommerce as the base price.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		$fields['products_prices_sale_by_cml'] =
		[
			'title' => __('Prices based on CommerceML data: sale', 'wc1c'),
			'type' => 'select',
			'description' => sprintf
			(
				'%s<hr><b>%s</b> - %s<br /><b>%s</b> - %s<br /><b>%s</b> - %s',
				__('The setting works when creating and updating products (goods). The sale price must be less than the regular price. Otherwise, it simply wont apply.', 'wc1c'),
				__('Do not use', 'wc1c'),
				__('Populating the sale prices data from CommerceML data will be skipped.', 'wc1c'),
				__('From first found', 'wc1c'),
				__('The first available price of all available prices for the product will be used as the sale price.', 'wc1c'),
				__('From specified name', 'wc1c'),
				__('The price with the specified name will be used as the sale price. If the price is not found by name, no value will be assigned.', 'wc1c')
			),
			'default' => 'no',
			'options' => $products_prices_by_cml_options
		];

		$fields['products_prices_sale_by_cml_from_name'] =
		[
			'title' => __('Prices based on CommerceML data: sale - name in 1C', 'wc1c'),
			'type' => 'text',
			'description' => __('Specify the name of the sale price in 1C, which is used for filling to WooCommerce as the sale price.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 370px;',
		];

		return $fields;
	}

	/**
	 * Configuration fields: products inventories
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsProductsInventories($fields)
	{
		$fields['title_products_inventories'] =
		[
			'title' => __('Inventories', 'wc1c'),
			'type' => 'title',
			'description' => __('Comprehensive settings for updating inventories based on data from the offer package.', 'wc1c'),
		];

		$fields['products_inventories_by_offers_quantity'] =
		[
			'title' => __('Filling inventories based on quantity from offers', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('It will be allowed to fill in the quantity of product stocks in WooCommerce based on the quantity received in 1C offers.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: catalog
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsCatalog($fields)
	{
		$fields['title_catalog'] =
		[
			'title' => __('Catalog', 'wc1c'),
			'type' => 'title',
			'description' => __('Settings for uploading the product catalog and additional data for it.', 'wc1c'),
		];

		$fields['catalog_export'] =
		[
			'title' => __('Export', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the box to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('It will allow the export of the product catalog upon request from the Flamix service.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: logs
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurationsFieldsLogs($fields)
	{
		$fields['title_logger'] =
		[
			'title' => __('Event logs', 'wc1c'),
			'type' => 'title',
			'description' => __('Maintaining event logs for the current configuration. You can view the logs through the extension or via FTP.', 'wc1c'),
		];

		$fields['logger_level'] =
		[
			'title' => __('Level for events', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => '300',
			'options' =>
			[
				'logger_level' => __('Use level for main events', 'wc1c'),
				'100' => __('DEBUG (100)', 'wc1c'),
				'200' => __('INFO (200)', 'wc1c'),
				'250' => __('NOTICE (250)', 'wc1c'),
				'300' => __('WARNING (300)', 'wc1c'),
				'400' => __('ERROR (400)', 'wc1c'),
			],
		];

		$fields['logger_files_max'] =
		[
			'title' => __('Maximum files', 'wc1c'),
			'type' => 'text',
			'description' => __('Log files created daily. This option on the maximum number of stored files. By default saved of the logs are for the last 30 days.', 'wc1c'),
			'default' => 10,
			'css' => 'min-width: 20px;',
		];

		return $fields;
	}
}