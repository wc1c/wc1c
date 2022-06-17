<?php namespace Wc1c\Wc\Products\Traits;

defined('ABSPATH') || exit;

/**
 * TypeProductTrait
 *
 * @package Wc1c\Wc
 */
trait TypeProductTrait
{
	/**
	 * Checks the product type.
	 *
	 * @param string|array $type Array or string of types.
	 *
	 * @return bool
	 */
	public function isType($type)
	{
		return $this->is_type($type);
	}
}