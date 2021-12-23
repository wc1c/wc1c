<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;

/**
 * Receiver
 *
 * @package Wc1c
 */
final class Receiver
{
	/**
	 * Receiver constructor.
	 */
	public function __construct()
	{
		// hook
		do_action(WC1C_PREFIX . 'receiver_before_loading');

		// run
		add_filter('parse_request', [$this, 'handleRequests']);

		// hook
		do_action(WC1C_PREFIX . 'receiver_after_loading');
	}

	/**
	 * Handle requests
	 */
	public function handleRequests()
	{
		$wc1c_receiver = wc1c()->getVar($_GET['wc1c-receiver'], false);

		if(false !== $wc1c_receiver)
		{
			wc1c()->define('WC1C_RECEIVER_REQUEST', true);

			if('yes' !== wc1c()->settings()->get('receiver', 'yes'))
			{
				wc1c()->log('receiver')->warning(__('Receiver is offline. Request reject.', 'wc1c'));
				die(__('Receiver is offline. Request reject.', 'wc1c'));
			}

			try
			{
				$configuration = new Configuration($wc1c_receiver);
			}
			catch(Exception $e)
			{
				wc1c()->log('receiver')->warning(__('Input unavailable', 'wc1c'));
				die(__('Input unavailable', 'wc1c'));
			}

			wc1c()->environment()->set('current_configuration_id', $wc1c_receiver);

			if($configuration->getStatus() !== 'active')
			{
				wc1c()->log('receiver')->warning(__('Configuration offline.', 'wc1c'));
				die(__('Configuration offline.', 'wc1c'));
			}

			$configuration->setDateActivity(time());
			$configuration->save();

			try
			{
				wc1c()->schemas()->init($configuration);
			}
			catch(Exception $e)
			{
				wc1c()->log('receiver')->error($e->getMessage(), $e);
				die('Exception: ' . $e->getMessage());
			}

			$action = false;

			ob_start();
			nocache_headers();

			$wc1c_receiver_action = 'wc1c_receiver';

			if(has_action($wc1c_receiver_action))
			{
				$action = true;
				do_action($wc1c_receiver_action);
			}

			if('' !== $wc1c_receiver)
			{
				$wc1c_receiver_action .= '_' . $configuration->getSchema();
			}

			if(has_action($wc1c_receiver_action))
			{
				$action = true;
				do_action($wc1c_receiver_action);
			}

			ob_end_clean();

			if(false === $action)
			{
				wc1c()->log('receiver')->warning(__('Receiver request is very bad!', 'wc1c'));
				die(__('Receiver request is very bad!', 'wc1c'));
			}
			die();
		}
	}
}