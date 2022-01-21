<?php namespace Wc1c\Admin\Configurations;

defined('ABSPATH') || exit;

use Wc1c\Admin\Traits\ProcessConfigurationTrait;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Traits\DatetimeUtilityTrait;
use Wc1c\Traits\SectionsTrait;
use Wc1c\Traits\SingletonTrait;
use Wc1c\Traits\UtilityTrait;

/**
 * MainUpdate
 *
 * @package Wc1c\Admin\Configurations
 */
class MainUpdate
{
	use SingletonTrait;
	use DatetimeUtilityTrait;
	use UtilityTrait;
	use SectionsTrait;
	use ProcessConfigurationTrait;

	/**
	 * Update processing
	 */
	public function process()
	{
		$configuration = $this->getConfiguration();
		$form = new UpdateForm();

		$form_data = $configuration->getOptions();
		$form_data['status'] = $configuration->getStatus();

		$form->load_saved_data($form_data);

		if(isset($_GET['form']) && $_GET['form'] === $form->get_id())
		{
			$data = $form->save();

			if($data)
			{
				$configuration->setStatus($data['status']);
				unset($data['status']);

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
		}

		add_action(WC1C_ADMIN_PREFIX . 'configurations_update_sidebar_show', [$this, 'outputSidebar'], 10);
		add_action(WC1C_ADMIN_PREFIX . 'configurations_update_show', [$form, 'outputForm'], 10);
	}

	/**
	 * Sidebar show
	 */
	public function outputSidebar()
	{
		$configuration = $this->getConfiguration();

		$args =
		[
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
		//$args['css'] = 'margin-top:-35px!important;';

		wc1c()->templates()->getTemplate('configurations/update_sidebar_item.php', $args);

		try
		{
			$schema = wc1c()->schemas()->get($configuration->getSchema());

			$args =
			[
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
}