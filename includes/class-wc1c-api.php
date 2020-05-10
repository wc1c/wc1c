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
		/**
		 * Api?
		 */
		$wc1c_api = false;

		/**
		 * Yes, api :)
		 */
		if(isset($_GET['wc1c-api']))
		{
			$wc1c_api = (int)$_GET['wc1c-api'];
		}

		/**
		 * Api valid, execute
		 */
		if(false !== $wc1c_api)
		{
			/**
			 * Api request define
			 */
			define('WC1C_API_REQUEST', true);

			/**
			 * Disable api
			 */
			if('yes' !== WC1C()->settings()->get('api', 'yes'))
			{
				die('Api offline');
			}

			/**
			 * Load configuration by id
			 */
			WC1C()->load_configurations($wc1c_api);

			/**
			 * Init
			 */
			WC1C()->init_configurations($wc1c_api);

			/**
			 * Get current configuration
			 */
			$configuration_data = WC1C()->get_configurations($wc1c_api);

			/**
			 * Not found
			 */
			if(false === $configuration_data)
			{
				die('Configuration not found!');
			}

			/**
			 * Set current id
			 */
			WC1C()->environment()->set('current_configuration_id', $wc1c_api);

			/**
			 * Initialize schema by id
			 */
			try
			{
				WC1C()->init_schemas($configuration_data['instance']->get_schema());
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
				$wc1c_api_action .= '_' . $configuration_data['instance']->get_schema();
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
				die('Api request is very bad!');
			}
			die();
		}
	}
}