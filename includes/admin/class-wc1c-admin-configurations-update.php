<?php
/**
 * Admin configurations update class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Update
{
	/**
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Wc1c_Admin_Configurations_Update constructor
	 */
	public function __construct()
	{
		add_action('wc1c_admin_configurations_show', [$this, 'template_output'], 10);

		$configuration_ready = true;
		$configuration_id = wc1c_get_var($_GET['config_id'],false);

		if(false === $configuration_id)
		{
			$configuration_ready = false;
		}

		try
		{
			$storage_configurations = Wc1c_Data_Storage::load('configuration');
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('init_schemas: exception - ' . $e->getMessage());
		}

		if(!$storage_configurations->is_existing_by_id($configuration_id))
		{
			throw new Wc1c_Exception_Runtime('init_schemas: $configuration is not exists');
		}

		try
		{
			$configuration = new Wc1c_Configuration($configuration_id);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('init_schemas: exception - ' . $e->getMessage());
		}

		try
		{
			WC1C()->init_schemas($configuration);
		}
		catch(Exception $e)
		{
			$configuration_ready = false;
		}

		if(false === $configuration_ready)
		{
			add_action('wc1c_admin_configurations_update_show', [$this, 'output_404'], 10);
			return;
		}

		//add_action('wc1c_admin_configurations_update_sidebar_show', array($this, 'output_sidebar'), 10);

		$form = new Wc1c_Admin_Configurations_Update_Form();

		$form_data = $configuration->get_options();
		$form_data['name'] = $configuration->get_name();
		$form_data['status'] = $configuration->get_status();

		add_action('wc1c_admin_configurations_update_show', [$form, 'output_form'], 10);
		add_action('wc1c_admin_configurations_update_sidebar_show', [$form, 'output_navigation'], 10);
	}

	/**
	 * Template output
	 */
	public function template_output()
	{
		wc1c_get_template('configurations/update.php');
	}

	/**
	 * Sidebar show
	 */
	public function output_sidebar()
	{
		$args = [
			'header' => '<h5 class="p-0 m-0">' . __('About configuration', 'wc1c') . '</h5>',
			'object' => $this
		];

		$configuration = $this->get_configuration();

		$body = '<ul class="list-group m-0 list-group-flush">';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Configuration ID: ', 'wc1c') . $configuration->get_id();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Schema ID: ', 'wc1c') . $configuration->get_schema();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date create: ', 'wc1c') . $configuration->get_date_create();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date modify: ', 'wc1c') . $configuration->get_date_modify();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date active: ', 'wc1c') . $configuration->get_date_activity();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Upload directory: ', 'wc1c') . WC1C()->environment()->get('wc1c_current_schema_upload_directory');
		$body .= '</li>';

		$body .= '</ul>';

		$args['body'] = $body;

		wc1c_get_template('configurations/update_sidebar_item.php', $args);

		try
		{
			$schema = WC1C()->get_schemas($configuration->get_schema());

			$args = [
				'header' => '<h5 class="p-0 m-0">' . __('About schema', 'wc1c') . '</h5>',
				'object' => $this
			];

			$body = '<ul class="list-group m-0 list-group-flush">';
			$body .= '<li class="list-group-item p-2 m-0">';
			$body .= __('Schema name: ', 'wc1c') . $schema->get_name();
			$body .= '</li>';

			$body .= '</ul>';

			$args['body'] = $body;

			wc1c_get_template('configurations/update_sidebar_item.php', $args);
		}
		catch(Exception $e){}
	}

	/**
	 * Error show
	 */
	public function output_404()
	{
		wc1c_get_template('configurations/404.php');
	}
}