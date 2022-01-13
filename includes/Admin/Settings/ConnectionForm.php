<?php namespace Wc1c\Admin\Settings;

defined('ABSPATH') || exit;

use Wc1c\Connection;
use Wc1c\Exceptions\Exception;

/**
 * ConnectionForm
 *
 * @package Wc1c\Admin\Settings
 */
class ConnectionForm extends Form
{
	/**
	 * @var bool Connection status
	 */
	public $status = false;

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

		$settings = wc1c()->settings('connection');

		$this->setSettings($settings);

		try
		{
			$this->connection = new Connection();
			$this->connection->setAppName(get_bloginfo());
		}
		catch(\Exception $e){}

		try
		{
			$this->apHandle();
		}
		catch(\Exception $e){}

		if('' !== $settings->get('token', ''))
		{
			$this->status = true;
		}

		if($this->status !== false)
		{
			add_filter(WC1C_PREFIX . $this->get_id() . '_form_load_fields', [$this, 'init_fields_connected'], 10);
		}
		else
		{
			add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'output'], 10);
		}

		$this->init();
	}

	/**
	 * Handle AP
	 *
	 * @return void
	 */
	public function apHandle()
	{
		if(isset($_GET['site_url'], $_GET['user_login']))
		{
			$site_url = $_GET['site_url'];
			$user_login = sanitize_key($_GET['user_login']);
			$password = '';
			$sold_url = remove_query_arg(['site_url', 'user_login', 'password']);

			if(isset($_GET['password']))
			{
				$password = sanitize_key($_GET['password']);
			}

			if($password !== '')
			{
				try
				{
					$this->settings->save(['token' => $password, 'login' => $user_login]);

					wc1c()->admin()->notices()->create
					(
						[
							'type' => 'update',
							'data' => sprintf
							(
								__( 'The user with the login %s on the site %s successfully connected to the current site.', 'wc1c'),
								'<strong>' . esc_html($user_login) . '</strong>',
								'<strong>' . esc_html($site_url) . '</strong>'
							)
						]
					);

					wp_safe_redirect($sold_url);
					die;
				}
				catch(Exception $e)
				{
					wc1c()->log()->addNotice('Settings is not successful save.', ['exception' => $e]);
				}
			}

			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'error',
					'data' => sprintf
					(
						__('Error connecting user with login %s on site %s to the current site. Please try again later.', 'wc1c'),
						'<strong>' . esc_html($user_login) . '</strong>',
						'<strong>' . esc_html($site_url) . '</strong>'
					)
				]
			);

			wp_safe_redirect($sold_url);
			die;
		}
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

		if($this->status)
		{
			try
			{
				$this->settings->save(['login' => '', 'token' => '']);
			}
			catch(Exception $e)
			{
				wc1c()->log()->addNotice('Settings is not successful save.', ['exception' => $e]);
			}

			wc1c()->admin()->notices()->create
			(
				[
					'type' => 'update',
					'data' => __('Disconnect successful. Reconnect is available', 'wc1c')
				]
			);

			$sold_url =  get_site_url() . add_query_arg('do_settings', 'connection');
		}
		else
		{
			$sold_url = $this->connection->buildUrl(get_site_url() . add_query_arg('do_settings', 'connection'));
		}

		wp_redirect($sold_url);
		die;
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
			'description' => __('The current application token for the user. This token can be revoked in your personal account on the WC1C website, as well as by clicking the Disconnect button.', 'wc1c'),
			'default' => '',
			'disabled' => true,
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

	/**
	 * Output
	 *
	 * @return void
	 */
	public function output()
	{
		wc1c()->templates()->getTemplate('connection/init.php');
	}
}