<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;

/**
 * Activation
 *
 * @package Wc1c
 */
final class Activation
{
	use SingletonTrait;

	public function __construct()
	{
		if(false === get_option('wc1c_version', false))
		{
			update_option('wc1c_wizard', 'setup');
		}
	}
}