<?php namespace Wc1c\Wc\Entities\Products;

defined('ABSPATH') || exit;

use WC_Product_Factory;
use Wc1c\Wc\Contracts\ProductContract;

/**
 * Factory
 *
 * @package Wc1c\Wc
 */
class Factory extends WC_Product_Factory
{
	/**
	 * Get a product.
	 *
	 * @param mixed $product_id int $product Product identifier.
	 *
	 * @return ProductContract|bool Product object or null if the product cannot be loaded.
	 */
	public function getProduct($product_id)
	{
		return $this->get_product($product_id);
	}

	/**
	 * Create a WC1C coding standards compliant class names.
	 *
	 * @param string $product_type Product type.
	 *
	 * @return string|false
	 */
	public function getClassnameFromProductType($product_type)
	{
		return self::get_classname_from_product_type($product_type);
	}

	/**
	 * Create a WC1C coding standards compliant class name.
	 *
	 * @param string $product_type Product type.
	 *
	 * @return string|false
	 */
	public static function get_classname_from_product_type($product_type)
	{
		switch($product_type)
		{
			case 'simple':
				return SimpleProduct::class;
				break;
			case 'variation':
				return VariationVariableProduct::class;
				break;
			case 'variable':
				return VariableProduct::class;
				break;
			default:
				return $product_type ? 'WC_Product_' . implode( '_', array_map( 'ucfirst', explode( '-', $product_type ) ) ) : false;
		}
	}

	/**
	 * Gets a product classname and allows filtering. Returns SimpleProduct::class if the class does not exist.
	 *
	 * @param int $product_id Product ID.
	 * @param string $product_type Product type.
	 *
	 * @return string
	 */
	public static function get_product_classname($product_id, $product_type)
	{
		$classname = apply_filters('woocommerce_product_class', self::get_classname_from_product_type($product_type), $product_type, 'variation' === $product_type ? 'product_variation' : 'product', $product_id);

		if(!$classname || !class_exists($classname))
		{
			$classname = SimpleProduct::class;
		}

		return $classname;
	}
}