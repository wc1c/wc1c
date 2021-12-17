<?php
/**
 * Namespace
 */
namespace Wc1c;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Exceptions\Exception;

/**
 * Input
 *
 * @package Wc1c
 */
class Input
{
	/**
	 * Input constructor.
	 */
	public function __construct()
	{
		// hook
		do_action(WC1C_PREFIX . 'input_before_loading');

		// run
		add_filter('parse_request', [$this, 'handleRequests']);

		// hook
		do_action(WC1C_PREFIX . 'input_after_loading');
	}

	/**
	 * Handle requests
	 */
	public function handleRequests()
	{
		$wc1c_input = wc1c_get_var($_GET['wc1c-input'], false);

		if(false !== $wc1c_input)
		{
			wc1c()->define('WC1C_INPUT_REQUEST', true);

			if('yes' !== wc1c()->settings()->get('input', 'yes'))
			{
				wc1c()->log()->warning(__('Background input is offline. Request reject.', 'wc1c'));
				die(__('Background input is offline. Request reject.', 'wc1c'));
			}

			try
			{
				$configuration = new Configuration($wc1c_input);
			}
			catch(Exception $e)
			{
				wc1c()->log()->warning(__('Input unavailable', 'wc1c'));
				die(__('Input unavailable', 'wc1c'));
			}

			wc1c()->environment()->set('current_configuration_id', $wc1c_input);

			if($configuration->getStatus() !== 'active')
			{
				wc1c()->log()->warning(__('Configuration offline.', 'wc1c'));
				die(__('Configuration offline.', 'wc1c'));
			}

			$configuration->setDateActivity();
			$configuration->save();

			try
			{
				wc1c()->initSchemas($configuration);
			}
			catch(Exception $e)
			{
				wc1c()->log()->error($e->getMessage(), $e);
				die('Exception: ' . $e->getMessage());
			}

			$action = false;

			ob_start();
			nocache_headers();

			$wc1c_api_action = 'wc1c_input';

			if(has_action($wc1c_api_action))
			{
				$action = true;
				do_action($wc1c_api_action);
			}

			if('' !== $wc1c_input)
			{
				$wc1c_api_action .= '_' . $configuration->getSchema();
			}

			if(has_action($wc1c_api_action))
			{
				$action = true;
				do_action($wc1c_api_action);
			}

			ob_end_clean();

			if(false === $action)
			{
				wc1c()->log()->warning(__('Input request is very bad!', 'wc1c'));
				die(__('Input request is very bad!', 'wc1c'));
			}
			die();
		}
	}
}