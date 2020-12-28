<?php
/**
 * Admin configurations create class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Create extends Wc1c_Admin_Abstract_Form
{
	/**
	 * Wc1c_Admin_Configurations_Create constructor
	 *
	 * @param bool $init
	 */
	public function __construct($init = true)
	{
		/**
		 * Form id
		 */
		$this->set_id('configurations-create');

		/**
		 * Auto init
		 */
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
		/**
		 * Init fields
		 */
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_main'), 10);

		/**
		 * Load form fields
		 */
		$this->load_fields();

		/**
		 * Load form saved data
		 */
		$this->load_saved_data();

		/**
		 * Form show
		 */
		add_action('wc1c_admin_configurations_create_show', array($this, 'output_form'), 10);

		/**
		 * Form save
		 */
		$this->save();
	}

	/**
	 * Form show
	 */
	public function output_form()
	{
		echo '<form method="post" action="">';
		echo wp_nonce_field('wc1c-admin-configurations-create-save', '_wc1c-admin-nonce');
		echo '<table class="form-table wc1c-admin-form-table">';
		$this->generate_html($this->get_fields(), true);
		echo '</table>';
		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="' . __('Create configuration', 'wc1c') . '"></p>';
		echo '</form>';
	}

	/**
	 * Save form data in DB
	 *
	 * @return bool
	 */
	public function save()
	{
		/**
		 * Post data
		 */
		$post_data = $this->get_posted_data();

		/**
		 * Xss
		 */
		if(!isset($post_data['_wc1c-admin-nonce']))
		{
			return false;
		}

		/**
		 * Xss
		 */
		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce'], 'wc1c-admin-configurations-create-save'))
		{
			WC1C_Admin()->add_message('error', __('Save error. Please retry.', 'wc1c'));

			return false;
		}

		/**
		 * All form fields validate
		 */
		foreach($this->get_fields() as $key => $field)
		{
			if('title' === $this->get_field_type($field) || 'raw' === $this->get_field_type($field))
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

		/**
		 * Data to save
		 */
		$data = $this->get_saved_data();

		/**
		 * Create new configuration
		 */
		$configuration = new Wc1c_Configuration($data);

		/**
		 * Set configuration status
		 */
		$configuration->set_status('draft');

		/**
		 * Save
		 */
		$saved = $configuration->save();

		/**
		 * Settings saved
		 */
		if($saved)
		{
			WC1C_Admin()->add_message('update', __('Configuration create success.', 'wc1c'));
		}
		else
		{
			WC1C_Admin()->add_message('error', __('Configuration create error. Please retry saving or change fields.', 'wc1c'));
		}

		return true;
	}

	/**
	 * Loading saved data
	 *
	 * @param array $saved_data
	 */
	public function load_saved_data($saved_data = array())
	{
		$form_fields = $this->get_fields();
		$saved_data = array_merge
		(
			array_fill_keys(array_keys($form_fields), ''),
			wp_list_pluck($form_fields, 'default')
		);

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
		$fields['config_name'] = array
		(
			'title' => __('Configuration name', 'wc1c'),
			'type' => 'text',
			'label' => __('Name of the configuration for easy use. You can enter any data up to 255 characters.', 'wc1c'),
			'description' => __('Used for convenient distribution of multiple configurations.', 'wc1c'),
			'default' => '',
			'css' => 'width: 100%;',
		);

		try
		{
			$schemas = WC1C()->get_schemas();
		}
		catch(Exception $e)
		{
			return $fields;
		}

		$options = [];
		foreach($schemas as $schema_id => $schema_object)
		{
			$options[$schema_id] = $schema_object->get_name() . ' (' . $schema_id . ')';
		}

		$fields['schema'] = array
		(
			'title' => __('Configuration schema', 'wc1c'),
			'type' => 'select',
			'description' => __('Each scheme has its own algorithms and settings. Use the appropriate scheme for your tasks.', 'wc1c'),
			'default' => 'default',
			'options' => $options
		);

		return $fields;
	}
}