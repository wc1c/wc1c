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
use Wc1c\Exceptions\Exception;
use Wc1c\Abstracts\FormAbstract;

/**
 * Class DeleteForm
 *
 * @package Wc1c\Admin\Configurations
 */
class DeleteForm extends FormAbstract
{
	/**
	 * DeleteForm constructor.
	 */
	public function __construct()
	{
		$this->set_id('configurations-delete');

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
		$fields['accept'] =
		[
			'title' => __('Delete confirmation', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('I confirm that Configuration will be permanently and irrevocably deleted from WooCommerce.', 'wc1c'),
			'default' => 'no',
		];

		return $fields;
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

		wc1c_get_template('configurations/delete_form.php', $args);
	}

	/**
	 * Save
	 *
	 * @return bool
	 */
	public function save()
	{
		$post_data = $this->get_posted_data();

		if(!isset($post_data['_wc1c-admin-nonce-configurations-delete']))
		{
			return false;
		}

		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce-configurations-delete'], 'wc1c-admin-configurations-delete-save'))
		{
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Delete error. Please retry.', 'wc1c')
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

				return false;
			}
		}

		$data = $this->get_saved_data();

		if(!isset($data['accept']) || $data['accept'] !== 'yes')
		{
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Delete error. Confirmation of final deletion is required.', 'wc1c')
				]
			);

			return false;
		}

		return true;
	}
}