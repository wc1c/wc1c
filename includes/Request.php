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
use Wc1c\Traits\SingletonTrait;

/**
 * Request
 *
 * @package Wc1c
 */
final class Request
{
	use SingletonTrait;

	/**
	 * Request constructor.
	 */
	public function __construct()
	{
		// hook
		do_action(WC1C_PREFIX . 'request_before_loading');

		// hook
		do_action(WC1C_PREFIX . 'request_after_loading');
	}

	/**
	 * Is input request?
	 *
	 * @return bool
	 */
	public function isInput()
	{
		if(wc1c()->getVar($_GET['wc1c-input'], false))
		{
			return true;
		}

		return false;
	}

	/**
	 * Is WC1C admin request?
	 *
	 * @return bool
	 */
	public function isWc1cAdmin()
	{
		if(false !== is_admin() && 'wc1c' === wc1c()->getVar($_GET['page'], ''))
		{
			return true;
		}

		return false;
	}
}