<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Settings;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Exceptions\Exception;
use Wc1c\Settings\MainSettings;

/**
 * Class MainForm
 *
 * @package Wc1c\Admin\Settings
 */
class MainForm extends Form
{
	/**
	 * MainForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->set_id('settings-main');
		$this->setSettings(new MainSettings());

		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_main'], 10);
		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_technical'], 10);
		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_configurations'], 10);
		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_interface'], 10);
		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_enable_data'], 10);
		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_logger'], 10);

		$this->init();
	}

	/**
	 * Add fields for Configurations
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_configurations($fields)
	{
		$fields['configurations_title'] =
		[
			'title' => __('Configurations', 'wc1c'),
			'type' => 'title',
			'description' => __('Settings for the Configurations.', 'wc1c'),
		];

		$fields['configurations_unique_name'] =
		[
			'title' => __('Unique configuration names', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Require unique names for configurations?', 'wc1c'),
			'description' => __('If enabled, you will need to provide unique names when configurations.', 'wc1c'),
			'default' => 'yes'
		];

		$fields['configurations_show_per_page'] =
		[
			'title' => __('Number in the list', 'wc1c'),
			'type' => 'text',
			'description' => __('The number of displayed configurations on one page.', 'wc1c'),
			'default' => 10,
			'css' => 'min-width: 20px;',
		];

		$fields['configurations_draft_delete'] =
		[
			'title' => __('Deleting drafts without trash', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable deleting drafts without placing them in the trash?', 'wc1c'),
			'description' => __('If enabled, configurations for connections in the draft status will be deleted without being added to the basket.', 'wc1c'),
			'default' => 'yes'
		];

		return $fields;
	}

	/**
	 * Add for Technical
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_technical($fields)
	{
		$fields['technical_title'] =
		[
			'title' => __('Technical settings', 'wc1c'),
			'type' => 'title',
			'description' => __('Used to set up the environment.', 'wc1c'),
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum request size', 'wc1c'),
			'type' => 'text',
			'description' => __('The setting must not take a size larger than specified in the server settings.', 'wc1c'),
			'default' => wc1c()->environment()->get('php_post_max_size'),
			'css' => 'min-width: 100px;',
		];

		$fields['php_max_execution_time'] =
		[
			'title' => __('Maximum time for execution PHP', 'wc1c'),
			'type' => 'text',
			'description' => __('Value is seconds. The setting must not take a execution time larger than specified in the PHP and web server settings (Apache, Nginx, etc).', 'wc1c'),
			'default' => wc1c()->environment()->get('php_max_execution_time'),
			'css' => 'min-width: 100px;',
		];

		return $fields;
	}

	/**
	 * Add for Main
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_main($fields)
	{
		$fields['api'] =
		[
			'title' => __('Input: background requests', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable Input: background requests?', 'wc1c'),
			'description' => __('It is used to receive background requests from 1C in exchange schemes. Do not disable this option if you do not know what it is for.', 'wc1c'),
			'default' => 'yes'
		];

		return $fields;
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
		$fields['interface_title'] =
		[
			'title' => __('Interface', 'wc1c'),
			'type' => 'title',
			'description' => __('Settings for the user interface.', 'wc1c'),
		];

		$fields['admin_interface'] =
		[
			'title' => __('Changing the interface', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Allow changes to WordPress and WooCommerce dashboard interface?', 'wc1c'),
			'description' => __('If enabled, new features will appear in the WordPress and WooCommerce interface according to the interface change settings.', 'wc1c'),
			'default' => 'yes'
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

		return $fields;
	}

	/**
	 * Add settings for logger
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_logger($fields)
	{
		$fields['logger_title'] =
		[
			'title' => __('Technical events', 'wc1c'),
			'type' => 'title',
			'description' => __('Used by technical specialists. Can leave it at that.', 'wc1c'),
		];

		$fields['logger_level'] =
		[
			'title' => __('Level', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wc1c'),
			'default' => '300',
			'options' =>
			[
				'' => __('Off', 'wc1c'),
				'100' => __('DEBUG', 'wc1c'),
				'200' => __('INFO', 'wc1c'),
				'250' => __('NOTICE', 'wc1c'),
				'300' => __('WARNING', 'wc1c'),
				'400' => __('ERROR', 'wc1c'),
			]
		];

		$fields['logger_wc1c'] =
		[
			'title' => __('Access to technical events', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Allow the WC1C team to access technical events?', 'wc1c'),
			'description' => __('If allowed, the WC1C team will be able to access technical events and release the necessary updates based on them.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Add settings for enabled data
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_enable_data($fields)
	{
		$fields['title_enable_data'] =
		[
			'title' => __('Enable data by objects', 'wc1c'),
			'type' => 'title',
			'description' => __('Specifying the ability to work with data by object types (data types).', 'wc1c'),
		];

		$fields['enable_data_products'] =
		[
			'title' => __('Products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with products (delete, change, add).', 'wc1c'),
			'default' => 'no'
		];

		$fields['enable_data_category'] =
		[
			'title' => __('Categories', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with categories (delete, change, add).', 'wc1c'),
			'default' => 'no'
		];

		$fields['enable_data_attributes'] =
		[
			'title' => __('Attributes', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with attributes (delete, change, add).', 'wc1c'),
			'default' => 'no'
		];

		$fields['enable_data_orders'] =
		[
			'title' => __('Orders', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with orders (delete, change, add).', 'wc1c'),
			'default' => 'no'
		];

		$fields['enable_data_images'] =
		[
			'title' => __('Images', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with images (delete, change, add).', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}
}