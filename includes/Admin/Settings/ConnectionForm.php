<?php namespace Wc1c\Admin\Settings;

defined('ABSPATH') || exit;

use Wc1c\Connection;
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
	 * @var Connection
	 */
	public $connection;

	/**
	 * ConnectionForm constructor.
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->set_id('settings-connection');

		$connectionSettings = new ConnectionSettings();

		try
		{
			$this->connection = new Connection();
			$this->connection->setAppName(get_bloginfo());
		}
		catch(\Exception $e){}

		$this->setSettings($connectionSettings);

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

		$saved_data = $this->get_saved_data();

		/**
		 * Создание пароля приложений по логину и паролю
		 */
		if(!isset($saved_data['token']))
		{
			if(empty($saved_data['login']))
			{
				wc1c()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => __('Connection error. Login is required.', 'wc1c')
					]
				);

				return false;
			}

			if(empty($saved_data['password']))
			{
				wc1c()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => __('Connection error. Password is required.', 'wc1c')
					]
				);

				return false;
			}

			/**
			 * Подключение по API
			 */
			try
			{
				$credentials['login'] = $saved_data['login'];
				$credentials['password'] = $saved_data['password'];

				$this->connection->setCredentials($credentials);
			}
			catch(\Exception $e)
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

			/**
			 * Создание пароля приложений
			 */
			try
			{
				$token = $this->connection->createToken();
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

			if(!$token)
			{
				wc1c()->admin()->notices()->create
				(
					[
						'type' => 'error',
						'data' => __('Connection error. Password or Login is invalid.', 'wc1c')
					]
				);

				return false;
			}

			unset($saved_data['password']);
			$saved_data['token'] = $token;
		}
		else
		{
			/**
			 * Удаление пароля приложений
			 */

		}

		try
		{
			$this->getSettings()->set($saved_data);
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
				'data' => __('Connection success.', 'wc1c')
			]
		);

		return true;
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