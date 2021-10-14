<?php
/**
 * Admin configurations remove form class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Remove_Form extends Abstract_Wc1c_Admin_Form
{
	/**
	 * Wc1c_Admin_Configurations_Remove_Form constructor.
	 */
	public function __construct()
	{
		$this->set_id('configurations-remove');
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

		wc1c_get_template('configurations/remove_form.php', $args);
	}

	/**
	 * Save form data in DB
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

		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce'], 'wc1c-admin-configurations-remove-save'))
		{
			WC1C_Admin()->add_message('error', __('Save error. Please retry.', 'wc1c'));
			return false;
		}

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

		$data = $this->get_saved_data();

		$configuration = new Wc1c_Configuration($data);

		$configuration->set_status('draft');

		$saved = $configuration->save();

		if($saved)
		{
			WC1C_Admin()->add_message('update', __('Configuration remove success.', 'wc1c'));
		}
		else
		{
			WC1C_Admin()->add_message('error', __('Configuration remove error. Please retry saving or change fields.', 'wc1c'));
		}

		return true;
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