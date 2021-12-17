<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Configurations;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Admin\Traits\ProcessConfigurationTrait;
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Traits\SingletonTrait;

/**
 * Class Update
 *
 * @package Wc1c\Admin\Configurations
 */
class Update
{
	use SingletonTrait;
	use ProcessConfigurationTrait;

	/**
	 * Update constructor.
	 */
	public function __construct()
	{
		$configuration_id = wc1c_get_var($_GET['configuration_id'], 0);

		if(false === $this->setConfiguration($configuration_id))
		{
			try
			{
				wc1c()->initSchemas($this->getConfiguration());
			}
			catch(Exception $e)
			{
				add_action(WC1C_ADMIN_PREFIX . 'configurations_update_show', [$this, 'outputSchemaError'], 10);
			}

			$this->process();
		}
		else
		{
			add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'outputError'], 10);
			return;
		}

		add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'output'], 10);
	}

	/**
	 * Update processing
	 */
	public function process()
	{
		$configuration = $this->getConfiguration();

		$form = new UpdateForm();

		$form->load_fields();

		$form_data = $configuration->getOptions();

		$form_data['name'] = $configuration->getName();
		$form_data['status'] = $configuration->getStatus();

		$form->load_saved_data($form_data);

		$data = $form->save();

		if($data)
		{
			$configuration->setStatus($data['status']);
			unset($data['status']);

			$configuration->setName($data['name']);
			unset($data['name']);

			$configuration->setDateModify();
			$configuration->setOptions($data);

			$saved = $configuration->save();

			if($saved)
			{
				wc1c()->admin()->notices()->create
				(
					[
						'type' => 'update',
						'data' => __('Configuration update success.', 'wc1c')
					]
				);
			}
			else
			{
				wc1c()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => __('Configuration update error. Please retry saving or change fields.', 'wc1c')
					]
				);
			}
		}

		add_action('wc1c_admin_configurations_update_sidebar_show', [$this, 'outputSidebar'], 10);
		add_action('wc1c_admin_configurations_update_show', [$form, 'outputForm'], 10);
	}

	/**
	 * Output error
	 */
	public function outputError()
	{
		wc1c_get_template('configurations/update_error.php');
	}

	/**
	 * Output schema error
	 */
	public function outputSchemaError()
	{
		wc1c_get_template('configurations/update_schema_error.php');
	}

	/**
	 * Output
	 *
	 * @return void
	 */
	public function output()
	{
		wc1c_get_template('configurations/update.php');
	}

	/**
	 * Sidebar show
	 */
	public function outputSidebar()
	{
		$configuration = $this->getConfiguration();

		$args = [
			'header' => '<h3 class="p-0 m-0">' . __('About configuration', 'wc1c') . '</h3>',
			'object' => $this
		];

		$body = '<ul class="list-group m-0 list-group-flush">';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Configuration ID: ', 'wc1c') . $configuration->getid();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Schema ID: ', 'wc1c') . $configuration->getschema();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date create: ', 'wc1c') . $configuration->getDateCreate();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date modify: ', 'wc1c') . $configuration->getDateModify();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date active: ', 'wc1c') . $configuration->getDateActivity();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Upload directory: ', 'wc1c') . '<div class="p-1 mt-1 bg-light">' . $configuration->getUploadDirectory() . '</div>';
		$body .= '</li>';

		$body .= '</ul>';

		$args['body'] = $body;

		wc1c_get_template('configurations/update_sidebar_item.php', $args);

		try
		{
			$schema = wc1c()->getSchemas($configuration->getSchema());

			$args = [
				'header' => '<h4 class="p-0 m-0">' . __('About schema', 'wc1c') . '</h4>',
				'object' => $this
			];

			$body = '<ul class="list-group m-0 list-group-flush">';
			$body .= '<li class="list-group-item p-2 m-0">';
			$body .= __('Schema name: ', 'wc1c') . $schema->getname();
			$body .= '</li>';

			$body .= '</ul>';

			$args['body'] = $body;

			wc1c_get_template('configurations/update_sidebar_item.php', $args);
		}
		catch(RuntimeException $e){}
	}
}