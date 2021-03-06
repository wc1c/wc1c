<?php
/**
 * Settings class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Settings extends Wc1c_Admin_Abstract_Form
{
	/**
	 * Wc1c_Admin_Settings constructor
	 *
	 * @param bool $init
	 */
	public function __construct($init = true)
	{
		$this->set_id('settings');

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
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_technical'), 10);
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_extensions'), 30);
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_admin'), 40);
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_enable_data'), 20);
		//add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_uninstall'), 50);

		$this->load_fields();

		$this->load_saved_data();

		add_action('wc1c_admin_' . $this->get_id() . '_form_show', array($this, 'output_form'), 10);
		add_action('wc1c_admin_settings_sidebar_show', array($this, 'output_navigation'), 10);

		$this->save();
	}

	/**
	 * Form show
	 */
	public function output_form()
	{
		$args =
			[
				'object' => $this
			];

		wc1c_get_template('settings/update_form.php', $args);
	}

	/**
	 * Save form data
	 *
	 * @return bool
	 */
	public function save()
	{
		$post_data = $this->get_posted_data();

		if(!isset($post_data['_wc1c-admin-nonce']))
		{
			return false;
		}

		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce'], 'wc1c-admin-settings-save'))
		{
			WC1C_Admin()->add_message('error', __('Save error. Please retry.', 'wc1c'));
			return false;
		}

		/**
		 * All form fields validate
		 */
		foreach($this->get_fields() as $key => $field)
		{
			if('title' === $this->get_field_type($field))
			{
				continue;
			}

			try
			{
				$this->saved_data[$key] = $this->get_field_value($key, $field, $post_data);
			}
			catch(Exception $e)
			{
				WC1C_Admin()->add_message('error', $e->getMessage());
			}
		}

		$saved = false;

		try
		{
			$saved = WC1C()->settings()->save($this->get_saved_data());
		}
		catch(Exception $e)
		{
			WC1C_Admin()->add_message('error', $e->getMessage());
		}

		if($saved)
		{
			WC1C_Admin()->add_message('update', __('Saving settings success.', 'wc1c'));
		}
		else
		{
			WC1C_Admin()->add_message('error', __('Saving settings error. Please retry saving or change settings.', 'wc1c'));
		}

		return true;
	}

	/**
	 * Loading saved data
	 *
	 * @param array $saved_data
	 */
	public function load_saved_data($saved_data = [])
	{
		if(empty($saved_data))
		{
			$saved_data = WC1C()->settings()->get_data();
		}

		if(!is_array($saved_data))
		{
			$form_fields = $this->get_fields();
			$saved_data = array_merge
			(
				array_fill_keys(array_keys($form_fields), ''),
				wp_list_pluck($form_fields, 'default')
			);
		}

		$saved_data = apply_filters('wc1c_admin_' . $this->get_id() . '_form_load_saved_data', $saved_data);

		$this->set_saved_data($saved_data);
	}

	/**
	 * Navigation show
	 */
	public function output_navigation()
	{
		$args = [
			'header' => '<h5 class="p-0 m-0">' . __('Fast navigation', 'wc1c') . '</h5>',
			'object' => $this
		];

		$body = '<div class="list-group m-0">';

		$form_fields = $this->get_fields();

		foreach($form_fields as $k => $v)
		{
			$type = $this->get_field_type($v);

			if($type !== 'title')
			{
				continue;
			}

			if(method_exists($this, 'generate_navigation_html'))
			{
				$body .= $this->{'generate_navigation_html'}($k, $v);
			}
		}

		$body .= '</div>';

		$args['body'] = $body;

		wc1c_get_template('settings/update_sidebar_item.php', $args);
	}

	/**
	 * Generate navigation HTML
	 *
	 * @param string $key - field key
	 * @param array $data - field data
	 *
	 * @return string
	 */
	public function generate_navigation_html($key, $data)
	{
		$field_key = $this->get_prefix_field_key($key);

		$defaults = array
		(
			'title' => '',
			'class' => '',
		);

		$data = wp_parse_args($data, $defaults);

		ob_start();
		?>
		<a class="list-group-item p-2 m-0 border-0" href="#<?php echo esc_attr($field_key); ?>"><?php echo wp_kses_post($data['title']); ?></a>
		<?php

		return ob_get_clean();
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
		$fields['title_enable_data'] = array
		(
			'title' => __('Enable data by objects', 'wc1c'),
			'type' => 'title',
			'description' => __('Specifying the ability to work with data by object types (data types).', 'wc1c'),
		);

		$fields['enable_data_products'] = array
		(
			'title' => __('Products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with products (delete, change, add).', 'wc1c'),
			'default' => 'yes'
		);

		$fields['enable_data_category'] = array
		(
			'title' => __('Categories', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with categories (delete, change, add).', 'wc1c'),
			'default' => 'no'
		);

		$fields['enable_data_attributes'] = array
		(
			'title' => __('Attributes', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with attributes (delete, change, add).', 'wc1c'),
			'default' => 'no'
		);

		$fields['enable_data_orders'] = array
		(
			'title' => __('Orders', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with orders (delete, change, add).', 'wc1c'),
			'default' => 'no'
		);

		$fields['enable_data_images'] = array
		(
			'title' => __('Images', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Ability to work with images (delete, change, add).', 'wc1c'),
			'default' => 'no'
		);

		return $fields;
	}

	/**
	 * Add settings for technical
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_technical($fields)
	{
		$fields['technical'] = array
		(
			'title' => __('Technical parameters', 'wc1c'),
			'type' => 'title',
			'description' => __('Used by technical specialists. Can leave it at that.', 'wc1c'),
		);

		$logger_path = WC1C()->logger()->get_path() . DIRECTORY_SEPARATOR . WC1C()->logger()->get_name();

		$fields['logger'] = array
		(
			'title' => __('Logging events', 'wc1c'),
			'type' => 'select',
			'description' => __('All events of the selected level will be recorded in the log file. The higher the level, the less data is recorded.', 'wc1c') . '<br/><b>' . __('Current file: ', 'wc1c') . '</b>' . $logger_path,
			'default' => '400',
			'options' => array
			(
				'' => __('Off', 'wc1c'),
				'100' => __('DEBUG', 'wc1c'),
				'200' => __('INFO', 'wc1c'),
				'250' => __('NOTICE', 'wc1c'),
				'300' => __('WARNING', 'wc1c'),
				'400' => __('ERROR', 'wc1c'),
			)
		);

		$fields['api'] = array
		(
			'title' => __('API', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('This API uses exchange schemas to receive requests from 1C and send data there.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['upload_directory_name'] = array
		(
			'title' => __('Name of upload directory', 'wc1c'),
			'type' => 'text',
			'description' => __('You need to change the name of the standard directory for security.', 'wc1c'),
			'default' => 'wc1c',
			'css' => 'min-width: 300px;',
		);

		$fields['php_post_max_size'] = array
		(
			'title' => __('Maximum request size', 'wc1c'),
			'type' => 'text',
			'description' => __('The setting must not take a size larger than specified in the server settings.', 'wc1c'),
			'default' => WC1C()->environment()->get('php_post_max_size'),
			'css' => 'min-width: 100px;',
		);

		$fields['php_max_execution_time'] = array
		(
			'title' => __('Maximum time for execution PHP', 'wc1c'),
			'type' => 'text',
			'description' => __('Value is seconds. The setting must not take a execution time larger than specified in the PHP and web server settings (Apache, Nginx, etc).', 'wc1c'),
			'default' => WC1C()->environment()->get('php_max_execution_time'),
			'css' => 'min-width: 100px;',
		);

		return $fields;
	}

	/**
	 * Add settings for admin panel
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_admin($fields)
	{
		$fields['title_admin'] = array
		(
			'title' => __('Admin interface', 'wc1c'),
			'type' => 'title',
			'description' => __('Configuring the output of information in the WordPress admin panel.', 'wc1c'),
		);

		$fields['admin_inject'] = array
		(
			'title' => __('Admin inject', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Embedding information from the plugin in the admin panel interface.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['admin_inject_products_column'] = array
		(
			'title' => __('Column in products list', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a column with information from 1C to the list of products.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['admin_inject_edit_products_metabox'] = array
		(
			'title' => __('Metabox in edit products', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a Metabox with information from 1C in edit products.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['admin_inject_orders_column'] = array
		(
			'title' => __('Column in orders list', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a column with information from 1C to the list of orders.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['admin_inject_edit_orders_metabox'] = array
		(
			'title' => __('Metabox in edit orders', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Output of a Metabox with information from 1C in edit orders.', 'wc1c'),
			'default' => 'yes'
		);

		return $fields;
	}

	/**
	 * Add settings for extensions
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_extensions($fields)
	{
		$fields['title_extensions'] = array
		(
			'title' => __('Extensions', 'wc1c'),
			'type' => 'title',
			'description' => __('Used by technical specialists. Can leave it at that.', 'wc1c'),
		);

		$fields['extensions'] = array
		(
			'title' => __('Support extensions', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Support for external extensions. If disabled, all third-party extensions will be unavailable.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['extensions_schemas'] = array
		(
			'title' => __('External schemas', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Support for external schemas. If disabled, all third-party schemas will be unavailable.', 'wc1c'),
			'default' => 'yes'
		);

		$fields['extensions_tools'] = array
		(
			'title' => __('External tools', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Enable', 'wc1c'),
			'description' => __('Support for external tools. If disabled, all third-party tools will be unavailable.', 'wc1c'),
			'default' => 'yes'
		);

		return $fields;
	}

	/**
	 * Add settings for Uninstall parameters
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_uninstall($fields)
	{
		$fields['uninstall'] = array
		(
			'title' => __('Uninstall parameters', 'wc1c'),
			'type' => 'title',
			'description' => __('Used by technical specialists. Can leave it at that.', 'wc1c'),
		);

		$fields['uninstall_remove_files'] = array
		(
			'title' => __('Remove files', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Deleting all files in their file system. If the checkbox is not checked, then the files will not be deleted from the /wp-content/uploads directory', 'wc1c'),
			'description' => __('Deleting files is disabled by default. It is best to delete files via FTP', 'wc1c'),
			'default' => 'no'
		);

		$fields['uninstall_remove_settings'] = array
		(
			'title' => __('Remove settings', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Deleting all the main plugin settings. If the checkbox is not checked, then the plugin settings will remain in the site database.', 'wc1c'),
			'description' => __('Deletion is disabled by default. Cleaning all the basic settings when finally deleting the plugin.', 'wc1c'),
			'default' => 'no'
		);

		$fields['uninstall_remove_tables'] = array
		(
			'title' => __('Remove tables', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Deleting all the main plugin tables. If the checkbox is not checked, then the plugin tables will remain in the site database.', 'wc1c'),
			'description' => __('Deletion is disabled by default. Cleaning all the basic tables when finally deleting the plugin.', 'wc1c'),
			'default' => 'no'
		);

		return $fields;
	}
}