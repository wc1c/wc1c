<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Columns\WooCommerce;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Traits\SingletonTrait;

/**
 * Orders
 *
 * @package Wc1c\Admin\Columns\WooCommerce
 */
final class Orders
{
	use SingletonTrait;

	/**
	 * Orders constructor.
	 */
	public function __construct()
	{
	}
}