<?php
/**
 * Api class
 *
 * @package Wc1c/Api
 */
defined('ABSPATH') || exit;

class Wc1c_Api
{
	/**
	 * Wc1c_Api constructor
	 */
	public function __construct()
	{
		do_action('wc1c_api_before_loading');

		$this->init_hooks();

		do_action('wc1c_api_after_loading');
	}

	/**
	 * Actions and filters
	 */
	private function init_hooks()
	{
		add_filter('parse_request', array($this, 'handle_api_requests'));
	}

	/**
	 * Handle api request
	 */
	public function handle_api_requests()
	{
		$wc1c_api = wc1c_get_var($_GET['wc1c-api'], false);

		if(false !== $wc1c_api)
		{
			wc1c_define('WC1C_API_REQUEST', true);

			if('yes' !== WC1C()->settings()->get('api', 'yes'))
			{
				die(__('Api offline', 'wc1c'));
			}

			try
			{
				$configuration = WC1C()->load_configuration($wc1c_api);
			}
			catch(Exception $e)
			{
				die(__('Api unavailable', 'wc1c'));
			}

			WC1C()->environment()->set('current_configuration_id', $wc1c_api);

			if($configuration->get_status() !== 'active')
			{
				die(__('Configuration offline', 'wc1c'));
			}

			try
			{
				WC1C()->init_schemas($configuration->get_schema());
			}
			catch(Exception $e)
			{
				die('Exception: ' . $e->getMessage());
			}

			/**
			 * Action flag
			 */
			$action = false;

			/**
			 * Buffer
			 */
			ob_start();

			/**
			 * Caching disable
			 */
			nocache_headers();

			/**
			 * Main action
			 */
			$wc1c_api_action = 'wc1c_api';

			/**
			 * Action found
			 */
			if(has_action($wc1c_api_action))
			{
				$action = true;
				do_action($wc1c_api_action);
			}

			/**
			 * Schema action
			 */
			if($wc1c_api !== '')
			{
				$wc1c_api_action .= '_' . $configuration->get_schema();
			}

			/**
			 * Schema action found
			 */
			if(has_action($wc1c_api_action))
			{
				$action = true;
				do_action($wc1c_api_action);
			}

			/**
			 * Buffer end
			 */
			ob_end_clean();

			/**
			 * Action not found
			 */
			if(false === $action)
			{
				die(__('Api request is very bad!', 'wc1c'));
			}
			die();
		}
	}
}