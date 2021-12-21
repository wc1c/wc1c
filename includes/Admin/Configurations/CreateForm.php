<?php namespace Wc1c\Admin\Configurations;

defined('ABSPATH') || exit;

use Wc1c\Configuration;
use Wc1c\Exceptions\Exception;
use Wc1c\Abstracts\FormAbstract;
use Wc1c\Traits\UtilityTrait;

/**
 * CreateForm
 *
 * @package Wc1c\Admin\Configurations
 */
class CreateForm extends FormAbstract
{
	use UtilityTrait;

	/**
	 * CreateForm constructor.
	 */
	public function __construct()
	{
		$this->set_id('configurations-create');

		add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_main'], 10);

		$this->load_fields();
	}

	/**
	 * Add for Main
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_main($fields)
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
			$schemas = wc1c()->getSchemas();
		}
		catch(Exception $e)
		{
			return $fields;
		}

		$options = [];
		foreach($schemas as $schema_id => $schema_object)
		{
			$options[$schema_id] = '(' . $schema_id . ') ' . $schema_object->getName();
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

	/**
	 * Save
	 *
	 * @return bool
	 * @throws Exception
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
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Configuration create error. Please retry.', 'wc1c')
				]
			);

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
				wc1c()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => $e->getMessage()
					]
				);
			}
		}

		$data = $this->get_saved_data();

		if(empty($data['name']))
		{
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Configuration create error. Name is required.', 'wc1c')
				]
			);

			return false;
		}

		if(empty($data['schema']))
		{
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Configuration create error. Schema is required.', 'wc1c')
				]
			);

			return false;
		}

		$configuration = new Configuration();
		$data_storage = $configuration->getStorage();
		$configuration->setStatus('draft');

		if('yes' === wc1c()->settings()->get('configurations_unique_name', 'yes') && $data_storage->isExistingByName($data['name']))
		{
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Configuration create error. Name is exists.', 'wc1c')
				]
			);

			return false;
		}

		$configuration->setName($data['name']);
		$configuration->setSchema($data['schema']);
		$configuration->setStatus('draft');

		if($configuration->save())
		{
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'update',
					'data' => __('Configuration create success. Configuration id: ' . $configuration->getId(), 'wc1c')
					          . ' (<a href="' . $this->utilityAdminConfigurationsGetUrl('update', $configuration->getId()) . '">' . __('edit configuration', 'wc1c') . '</a>)'
				]
			);

			$this->set_saved_data([]);
			return true;
		}

		wc1c()->admin()->notices()->create
		(
			[
				'type' => 'error',
				'data' => __('Configuration create error. Please retry saving or change fields.', 'wc1c')
			]
		);

		return false;
	}

	/**
	 * Form show
	 */
	public function outputForm()
	{
		$args =
		[
			'object' => $this
		];

		wc1c()->templates()->getTemplate('configurations/create_form.php', $args);
	}
}