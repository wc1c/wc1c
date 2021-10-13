<?php
/**
 * Admin configurations create form class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Create_Form extends Abstract_Wc1c_Admin_Form
{
	/**
	 * Wc1c_Admin_Configurations_Create_Form constructor
	 */
	public function __construct()
	{
		$this->set_id('configurations-create');
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

		wc1c_get_template('configurations/create_form.php', $args);
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

		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce'], 'wc1c-admin-configurations-create-save'))
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

		if(empty($data['name']))
		{
			WC1C_Admin()->add_message('error', __('Configuration create error. Name is required.', 'wc1c'));
			return false;
		}

		if(empty($data['schema']))
		{
			WC1C_Admin()->add_message('error', __('Configuration create error. Select schema is required.', 'wc1c'));
			return false;
		}

		$configuration = new Wc1c_Configuration();

		if('yes' === WC1C()->settings()->get('configurations_unique_names', 'no'))
		{
			$configuration_storage = $configuration->get_storage();

			if($configuration_storage->is_existing_by_name($data['name']))
			{
				WC1C_Admin()->add_message('error', __('Configuration create error. Name is exists.', 'wc1c'));
				return false;
			}
		}

		$configuration->set_status('draft');
		$configuration->set_name($data['name']);
		$configuration->set_schema($data['schema']);

		if($configuration->save())
		{
			WC1C_Admin()->add_message
			(
				'update',
				__('Configuration create success. Configuration created id: ' . $configuration->get_id(), 'wc1c')
				. ' (<a href="' . wc1c_admin_configurations_get_url('update', $configuration->get_id()) . '">' . __('edit configuration', 'wc1c') . '</a>)'
			);

			$this->set_saved_data([]);
			return true;
		}

		WC1C_Admin()->add_message('error', __('Configuration create error. Please retry saving or change fields.', 'wc1c'));
		return false;
	}
}