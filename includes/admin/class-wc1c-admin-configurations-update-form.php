<?php
/**
 * Admin configurations update form class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Update_Form extends Abstract_Wc1c_Admin_Form
{
	/**
	 * Wc1c_Admin_Configurations_Update_Form constructor.
	 */
	public function __construct()
	{
		$this->set_id('configurations-update');

		$this->title_numeric = true;

		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_main'), 0);

		$this->load_fields();
	}

	/**
	 * Output
	 */
	public function output_form()
	{
		$args =
        [
            'object' => $this
        ];

		wc1c_get_template('configurations/update_form.php', $args);
	}

	/**
	 * Navigation show
	 */
	public function output_navigation()
	{
		$args =
        [
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

		wc1c_get_template('configurations/update_sidebar_item.php', $args);
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
	 * Save form data in DB
	 *
	 * @throws Exception
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

		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce'], 'wc1c-admin-configurations-update-save'))
		{
			WC1C_Admin()->add_message('error', __('Save error. Please retry.', 'wc1c'));

			return false;
		}

		/**
		 * All form fields validate
		 */
		foreach($this->get_fields() as $key => $field)
		{
		    $field_type = $this->get_field_type($field);

			if('title' === $field_type || 'raw' === $field_type)
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

		$configuration_id = WC1C()->environment()->get('current_configuration_id', 0);

		$configuration_data = WC1C()->get_configurations($configuration_id);

		$data = $this->get_saved_data();

		$configuration_data->set_status($data['status']);
		unset($data['status']);

		$configuration_data->set_name($data['config_name']);
		unset($data['config_name']);

		$configuration_data->set_date_modify();
		$configuration_data->set_options($data);

		$saved = $configuration_data->save();

		if($saved)
		{
			WC1C_Admin()->add_message('update', __('Configuration update success.', 'wc1c'));
		}
		else
		{
			WC1C_Admin()->add_message('error', __('Configuration update error. Please retry saving or change fields.', 'wc1c'));
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
		$form_fields = $this->get_fields();

		/**
		 * Default value for form fields
		 */
		$form_data = array_merge
		(
			array_fill_keys(array_keys($form_fields), ''),
			wp_list_pluck($form_fields, 'default')
		);

		/**
		 * Merge saved data & form fields
		 */
		if(is_array($saved_data) && count($saved_data) > 0)
		{
			$saved_data = array_merge($form_data, $saved_data);
		}

		/**
		 * Change saved data from external code
		 */
		$saved_data = apply_filters('wc1c_admin_' . $this->get_id() . '_form_load_saved_data', $saved_data);

		$this->set_saved_data($saved_data);
	}

	/**
	 * Init fields for main settings
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_main($fields)
	{
		$fields['name'] =
		[
			'title' => __('Configuration name', 'wc1c'),
			'type' => 'text',
			'label' => __('Name of the configuration for easy use. You can enter any data up to 255 characters.', 'wc1c'),
			'description' => __('Used for convenient distribution of multiple configurations.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 200px;width:100%;',
		];

		$options =
		[
			'active' => wc1c_configurations_get_statuses_label('active'),
			'inactive' => wc1c_configurations_get_statuses_label('inactive')
		];

		$fields['status'] =
		[
			'title' => __('Configuration status', 'wc1c'),
			'type' => 'select',
			'description' => __('Current configuration status.', 'wc1c'),
			'default' => 'inactive',
			'options' => $options
		];

		return $fields;
	}
}