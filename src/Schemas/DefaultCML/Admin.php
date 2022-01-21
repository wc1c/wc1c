<?php namespace Wc1c\Schemas\DefaultCML;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;
use Wc1c\Traits\UtilityTrait;

/**
 * Admin
 *
 * @package Wc1c\Schemas\DefaultCML
 */
class Admin
{
	use SingletonTrait;
	use UtilityTrait;

	/**
	 * @var Core
	 */
	protected $core;

	/**
	 * @return Core
	 */
	public function core()
	{
		return $this->core;
	}

	/**
	 * @param Core $core
	 */
	public function setCore($core)
	{
		$this->core = $core;
	}

	/**
	 * @return void
	 */
	public function initConfigurations()
	{
		add_filter(WC1C_PREFIX . 'configurations-update_form_load_fields', [$this, 'configurations_fields_auth'], 10, 1);
		add_filter(WC1C_PREFIX . 'configurations-update_form_load_fields', [$this, 'configurations_fields_processing'], 10, 1);
		add_filter(WC1C_PREFIX . 'configurations-update_form_load_fields', [$this, 'configurations_fields_tech'], 10, 1);
	}

	/**
	 * Configuration fields: processing
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurations_fields_processing($fields)
	{
		$fields['title_processing'] =
		[
			'title' => __('Processing details', 'wc1c'),
			'type' => 'title',
			'description' => __('Changing the behavior of the file processing.', 'wc1c'),
		];

		$fields['skip_file_processing'] =
		[
			'title' => __('Skip processing of files', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Disabling the actual processing of CommerceML files. Files will be accepted, but instead of processing them, they will be skipped with successful completion of processing.', 'wc1c'),
			'default' => 'no'
		];

		$fields['delete_files_after_processing'] =
		[
			'title' => __('Deleting CommerceML files after processing', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If deletion is disabled, the exchange files will remain in the directories until the next exchange. Otherwise, all processed files will be deleted immediately after error-free processing.', 'wc1c'),
			'default' => 'no'
		];

		$fields['delete_zip_files_after_extract'] =
		[
			'title' => __('Deleting ZIP files after extract', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If deletion is disabled, the exchange ZIP files will remain in the directories until the next exchange.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: tech
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurations_fields_tech($fields)
	{
		$fields['title_tech'] =
		[
			'title' => __('Technical details', 'wc1c'),
			'type' => 'title',
			'description' => __('Changing processing behavior for compatibility of the environment and other systems.', 'wc1c'),
		];

		$logger_path = $this->core()->configuration()->getUploadDirectory('logs');

		$fields['logger'] =
		[
			'title' => __('Logging events', 'wc1c'),
			'type' => 'select',
			'description' => __('Can enable logging, specify the level of error that to benefit from logging.
			 Can send reports to developer. All sensitive data in the report are deleted.
			  By default, the error rate should not be less than ERROR.', 'wc1c'). '<br/><b>' . __('Current path: ', 'wc1c') . '</b>' . $logger_path,
			'default' => '400',
			'options' =>
			[
				'' => __('Off', 'wc1c'),
				'100' => __('DEBUG', 'wc1c'),
				'200' => __('INFO', 'wc1c'),
				'250' => __('NOTICE', 'wc1c'),
				'300' => __('WARNING', 'wc1c'),
				'400' => __('ERROR', 'wc1c'),
				'500' => __('CRITICAL', 'wc1c'),
				'550' => __('ALERT', 'wc1c'),
				'600' => __('EMERGENCY', 'wc1c')
			]
		];

		$fields['convert_cp1251'] =
		[
			'title' => __('Converting to Windows-1251', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Data from utf-8 will be converted to Windows-1251 encoding. Use this feature for compatibility with older versions of 1C.', 'wc1c'),
			'default' => 'no'
		];

		$fields['php_post_max_size'] =
		[
			'title' => __('Maximum request size', 'wc1c'),
			'type' => 'text',
			'description' => __('The setting must not take a size larger than specified in the server settings.', 'wc1c'),
			'default' => WC1C()->environment()->get('php_post_max_size'),
			'css' => 'min-width: 100px;',
		];

		$fields['file_zip'] =
		[
			'title' => __('Support for data compression', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('1C can transfer files in archives to reduce the number of HTTP requests and compress data. In this case, the load may increase when unpacking archives, or even it may be impossible to unpack due to server restrictions.', 'wc1c'),
			'default' => 'no'
		];

		$fields['receiver_check_auth_key_disabled'] =
		[
			'title' => __('Receiver: disable check auth key', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('When enabled, verification of authorization data will be completely disabled. The authenticity of requests from 1C will not be checked.', 'wc1c'),
			'default' => 'no'
		];

		return $fields;
	}

	/**
	 * Configuration fields: auth
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurations_fields_auth($fields)
	{
		$fields['title_auth'] =
		[
			'title' => __('Requests authorization', 'wc1c'),
			'type' => 'title',
			'description' => __('Data for authorization of requests. These settings will connect 1C.', 'wc1c'),
		];

		$url_raw = get_site_url(null, '/?wc1c-receiver=' . $this->core()->configuration()->getId() . '&get_param');
		$url_raw = '<p class="input-text p-2 bg-light regular-input wc1c_urls">' . esc_html($url_raw) . '</p>';

		$fields['url_requests'] =
		[
			'title' => __('Requests URL', 'wc1c'),
			'type' => 'raw',
			'raw' => $url_raw,
			'description' => __('This address is entered in the exchange settings on the 1C side. It will receive requests from 1C.', 'wc1c'),
		];

		$fields['user_login'] =
		[
			'title' => __('Login to connect', 'wc1c'),
			'type' => 'text',
			'description' => __('Enter the username to connect from 1C. It should be the same as when setting up in 1C.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		];

		$fields['user_password'] =
		[
			'title' => __('Password to connect', 'wc1c'),
			'type' => 'text',
			'description' => __('Enter the users password to connect from 1C. It must be the same as when setting up in 1C.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		];

		return $fields;
	}
}