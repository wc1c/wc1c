<?php
/**
 * Admin configurations create class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Create
{
	/**
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Wc1c_Admin_Configurations_Create constructor
	 */
	public function __construct()
	{
		$form = new Wc1c_Admin_Configurations_Create_Form();

		$form->load_fields($this->get_form_fields());

		if(!empty($form->get_posted_data()))
		{
			$form->save();
		}

		add_action('wc1c_admin_configurations_create_show', [$form, 'output_form'], 10);
		add_action('wc1c_admin_configurations_show', [$this, 'output'], 10);
	}

	/**
	 * Output
	 */
	public function output()
	{
		wc1c_get_template('configurations/create.php');
	}

	/**
	 * Form fields
	 *
	 * @return array
	 */
	public function get_form_fields()
	{
		$fields['name'] =
		[
			'title' => __('Configuration name', 'wc1c'),
			'type' => 'text',
			'label' => __('Name of the configuration for easy use. You can enter any data up to 255 characters.', 'wc1c'),
			'description' => __('Used for convenient distribution of multiple configurations.', 'wc1c'),
			'default' => '',
			'css' => 'width: 100%;',
		];

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
			$options[$schema_id] = $schema_id . ' - ' . $schema_object->get_name();
		}

		$fields['schema'] =
		[
			'title' => __('Configuration schema', 'wc1c'),
			'type' => 'select',
			'description' => __('Each scheme has its own algorithms and settings. Use the appropriate scheme for your tasks.', 'wc1c'),
			'default' => 'default',
			'options' => $options
		];

		return $fields;
	}
}