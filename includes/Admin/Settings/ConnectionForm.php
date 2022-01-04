<?php namespace Wc1c\Admin\Settings;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
use Wc1c\Settings\ConnectionSettings;

/**
 * ConnectionForm
 *
 * @package Wc1c\Admin\Settings
 */
class ConnectionForm extends Form
{
	/**
	 * ConnectionForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->set_id('settings-connection');

		$connectionSettings = new ConnectionSettings();

		$this->setSettings($connectionSettings);

		add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'before_form_show'], 10);

		if($connectionSettings->isConnected())
		{
			add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_connected'], 10);
		}
		else
		{
			add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_main'], 10);
		}

		$this->init();
	}

	/**
	 * Save
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

		if(empty($post_data) || !wp_verify_nonce($post_data['_wc1c-admin-nonce'], 'wc1c-admin-settings-save'))
		{
			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => __('Connection error. Please retry.', 'wc1c')
				]
			);

			return false;
		}

		/**
		 * All form fields validate
		 */
		foreach($this->get_fields() as $key => $field)
		{
			if('title' === $this->get_field_type($field))
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

		try
		{
			$this->getSettings()->set($this->get_saved_data());
			$this->getSettings()->save();
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

		wc1c()->admin()->notices()->create
		(
			[
				'type' => 'update',
				'data' => __('Save success.', 'wc1c')
			]
		);

		return true;
	}

	/**
	 * Show description
	 */
	public function before_form_show()
	{
		wc1c()->templates()->getTemplate('/connection/description.php');
	}

	/**
	 * Connected fields
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_connected($fields)
	{
		$fields['connected_title'] =
		[
			'title' => __('Site is connected to WC1C', 'wc1c'),
			'type' => 'title',
			'description' => __('To create a new connection, need to disconnect the current connection.', 'wc1c'),
		];

		$fields['login'] =
		[
			'title' => __('Login', 'wc1c'),
			'type' => 'text',
			'description' => __('Connected login from the WC1C website.', 'wc1c'),
			'default' => '',
			'disabled' => true,
			'css' => 'min-width: 300px;',
		];

		$fields['token'] =
		[
			'title' => __('App token', 'wc1c'),
			'type' => 'text',
			'description' => __('The current application token for the user. This token can be revoked in your personal account on the WC1C website, as well as by clicking the Disconnect from WC1C button.', 'wc1c'),
			'default' => '',
			'disabled' => true,
			'css' => 'min-width: 300px;',
		];

		return $fields;
	}

	/**
	 * Main fields
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function init_fields_main($fields)
	{
		$fields['main_title'] =
		[
			'title' => __('Site is not connected to WC1C', 'wc1c'),
			'type' => 'title',
			'description' => __('To create a new connection, need to enter a username and password from the WC1C website, or follow the link and authorize the application on the WC1C website.', 'wc1c'),
		];

		$fields['login'] =
		[
			'title' => __('Login', 'wc1c'),
			'type' => 'text',
			'description' => __('The login when registering on the WC1C website.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 300px;',
		];

		$fields['password'] =
		[
			'title' => __('Password', 'wc1c'),
			'type' => 'text',
			'description' => __('The current password on the WC1C site for the user. This password is not saved on site. A token for the application will be generated instead.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 300px;',
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

		wc1c()->templates()->getTemplate('connection/form.php', $args);
	}
}