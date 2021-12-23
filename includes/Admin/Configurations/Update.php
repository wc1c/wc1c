<?php namespace Wc1c\Admin\Configurations;

defined('ABSPATH') || exit;

use Wc1c\Admin\Traits\ProcessConfigurationTrait;
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Traits\DatetimeUtilityTrait;
use Wc1c\Traits\SingletonTrait;
use Wc1c\Traits\UtilityTrait;

/**
 * Update
 *
 * @package Wc1c\Admin\Configurations
 */
class Update
{
	use SingletonTrait;
	use ProcessConfigurationTrait;
	use DatetimeUtilityTrait;
	use UtilityTrait;

	/**
	 * Update constructor.
	 */
	public function __construct()
	{
		$configuration_id = wc1c()->getVar($_GET['configuration_id'], 0);

		if(false === $this->setConfiguration($configuration_id))
		{
			try
			{
				wc1c()->schemas()->init($this->getConfiguration());
			}
			catch(Exception $e)
			{
				add_action(WC1C_ADMIN_PREFIX . 'configurations_update_show', [$this, 'outputSchemaError'], 10);
				add_filter(WC1C_ADMIN_PREFIX . 'configurations_update_schema_error_text', [$this, 'outputSchemaErrorText'], 10, 1);

				wc1c()->log()->notice('Schema is not initialize', ['exception' => $e]);
			}

			$this->process();
		}
		else
		{
			add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'outputError'], 10);

			wc1c()->log()->notice('Configuration update is not available', ['configuration_id' => $configuration_id]);
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

			$configuration->setDateModify(time());
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

		add_action(WC1C_ADMIN_PREFIX . 'configurations_update_sidebar_show', [$this, 'outputSidebar'], 10);
		add_action(WC1C_ADMIN_PREFIX . 'configurations_update_show', [$form, 'outputForm'], 10);
	}

	/**
	 * Output error
	 */
	public function outputError()
	{
		$args['back_url'] = $this->utilityAdminConfigurationsGetUrl('all');

		wc1c()->templates()->getTemplate('configurations/update_error.php', $args);
	}

	/**
	 * Output schema error
	 */
	public function outputSchemaError()
	{
		wc1c()->templates()->getTemplate('configurations/update_schema_error.php');
	}

	/**
	 * Output
	 *
	 * @return void
	 */
	public function output()
	{
		$args['back_url'] = $this->utilityAdminConfigurationsGetUrl('all');

		wc1c()->templates()->getTemplate('configurations/update.php', $args);
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
		$body .= __('ID: ', 'wc1c') . $configuration->getId();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Schema ID: ', 'wc1c') . $configuration->getSchema();
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date create: ', 'wc1c') . $this->utilityPrettyDate($configuration->getDateCreate());
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date modify: ', 'wc1c') . $this->utilityPrettyDate($configuration->getDateModify());
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Date active: ', 'wc1c') . $this->utilityPrettyDate($configuration->getDateActivity());
		$body .= '</li>';
		$body .= '<li class="list-group-item p-2 m-0">';
		$body .= __('Upload directory: ', 'wc1c') . '<div class="p-1 mt-1 bg-light">' . $configuration->getUploadDirectory() . '</div>';
		$body .= '</li>';

		$body .= '</ul>';

		$args['body'] = $body;

		wc1c()->templates()->getTemplate('configurations/update_sidebar_item.php', $args);

		try
		{
			$schema = wc1c()->schemas()->get($configuration->getSchema());

			$args = [
				'header' => '<h3 class="p-0 m-0">' . __('About schema', 'wc1c') . '</h3>',
				'object' => $this
			];

			$body = '<ul class="list-group m-0 list-group-flush">';
			$body .= '<li class="list-group-item p-2 m-0">';
			$body .= $schema->getDescription();
			$body .= '</li>';

			$body .= '</ul>';

			$args['body'] = $body;

			wc1c()->templates()->getTemplate('configurations/update_sidebar_item.php', $args);
		}
		catch(RuntimeException $e){}
	}

	/**
	 * @param $text
	 *
	 * @return string
	 */
	public function outputSchemaErrorText($text)
	{
		$new_text = __('The exchange scheme on the basis of which created configuration is unavailable .', 'wc1c');

		$new_text .= '<br />' . __('Install the missing schema to work this configuration, change the status and name, or delete the configuration.', 'wc1c');

		return $new_text;
	}
}