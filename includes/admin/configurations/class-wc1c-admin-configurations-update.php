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
	 * Current configuration
	 *
	 * @var Wc1c_Configuration|null
	 */
	private $configuration = null;

	/**
	 * @var null|Wc1c_Admin_Configurations_Update_Form
	 */
	private $form = null;

	/**
	 * Wc1c_Admin_Configurations_Update constructor
	 *
	 * @param bool $init
	 */
	public function __construct($init = true)
	{
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
		$configuration_id = WC1C()->environment()->get('current_configuration_id', false);

		if($configuration_id === false)
		{
			return;
		}

		try
		{
			$configuration = WC1C()->load_configuration($configuration_id);
		}
		catch(Exception $e)
		{
			WC1C()->logger()->notice($e->getMessage());
			add_action('wc1c_admin_configurations_update_show', array($this, 'output_404'), 10);
			return;
		}

		$this->set_configuration($configuration);

		try
		{
			WC1C()->init_schemas($configuration->get_schema());
		}
		catch(Exception $e)
		{
			WC1C()->logger()->notice($e->getMessage());
			add_action('wc1c_admin_configurations_update_show', array($this, 'output_404'), 10);
			return;
		}

		add_action('wc1c_admin_configurations_update_sidebar_show', array($this, 'output_sidebar'), 10);

		/**
		 * Form
		 */
		$form = new Wc1c_Admin_Configurations_Update_Form(false);

		$form_data = $configuration->get_options();
		$form_data['config_name'] = $configuration->get_name();
		$form_data['status'] = $configuration->get_status();
		$form->load_saved_data($form_data);
		$form->init();

		$this->set_form($form);
	}

	/**
	 * @return Wc1c_Admin_Configurations_Update_Form|null
	 */
	public function get_form()
	{
		return $this->form;
	}

	/**
	 * @param Wc1c_Admin_Configurations_Update_Form|null $form
	 */
	public function set_form($form)
	{
		$this->form = $form;
	}

	/**
	 * @return Wc1c_Configuration|null
	 */
	public function get_configuration()
	{
		return $this->configuration;
	}

	/**
	 * @param Wc1c_Configuration $configuration
	 */
	public function set_configuration($configuration)
	{
		$this->configuration = $configuration;
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

		wc1c_get_template('configurations/update_form.php', $args);
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