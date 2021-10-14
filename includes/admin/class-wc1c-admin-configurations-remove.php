<?php
/**
 * Admin configurations remove class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Remove extends Abstract_Wc1c_Admin_Form
{
	/**
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Wc1c_Admin_Configurations_Remove constructor.
	 *
	 * @param bool $init
	 */
	public function __construct($init = true)
	{
		$this->set_id('configurations-remove');

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
		add_filter('wc1c_admin_' . $this->get_id() . '_form_load_fields', array($this, 'init_fields_main'), 10);

		$this->load_fields();
		$this->load_saved_data();

		$this->save();

		add_action('wc1c_admin_configurations_remove_show', array($this, 'output_form'), 10);
		add_action('wc1c_admin_configurations_show', array($this, 'template_output'), 10);
	}

	/**
	 * Template output
	 */
	public function template_output()
	{
		wc1c_get_template('configurations/remove.php');
	}

	/**
	 * Form show
	 */
	public function output_form()
	{
		echo '<form method="post" action="">';
		wp_nonce_field('wc1c-admin-configurations-remove-save', '_wc1c-admin-nonce', false, true);
		echo '<table class="form-table wc1c-admin-form-table bg-white">';
		$this->generate_html($this->get_fields(), true);
		echo '</table>';
		echo '<p class="submit"><input type="submit" name="submit" id="submit" class="button button-danger" value="' . __('Remove configuration', 'wc1c') . '"></p>';
		echo '</form>';
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