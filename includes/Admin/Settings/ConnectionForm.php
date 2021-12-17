<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Settings;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Exceptions\Exception;
use Wc1c\Settings\ConnectionSettings;

/**
 * Class ConnectionForm
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
	 * Show description
	 */
	public function before_form_show()
	{
		wc1c_get_template('/connection/description.php');
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

		wc1c_get_template('connection/form.php', $args);
	}
}