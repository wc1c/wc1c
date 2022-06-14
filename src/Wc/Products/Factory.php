<?php namespace Wc1c\Wc\Products;

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

	/**
	 * Получение продукта или продуктов по идентификатору продукта из 1С
	 *
	 * @param string $id
	 *
	 * @return bool|ProductContract
	 */
	public function getProductByExternalId($id)
	{
		$product_id = $this->findProductIdByExternalId($id);

		if(0 === $product_id)
		{
			return false;
		}

		return $this->getProduct($product_id);
	}

	/**
	 * Получение идентификатора товара по идентификатору товара из 1C
	 *
	 * @param int|string $id
	 *
	 * @return int
	 */
	public function findProductIdByExternalId($id)
	{
		$args =
		[
			'post_type' => ['product', 'product_variation'],
			'post_status' => implode(',', get_post_statuses()),
			'meta_key' => '_wc1c_external_id',
			'meta_value' => $id,
			'posts_per_page' => -1,
			'fields' => 'ids'
		];

		$posts = get_posts($args);
		$product_id = 0;

		if(is_array($posts) && count($posts) === 1)
		{
			$product_id = reset($posts);
		}

		return $product_id;
	}

	/**
	 * Получение продукта или продуктов по наименованию продукта из WooCommerce
	 *
	 * @param string $name Наименование искомого продукта
	 *
	 * @return false|ProductContract
	 */
	public function getProductByName($name)
	{
		$product_id = $this->findProductIdByName($name);

		if(0 === $product_id)
		{
			return false;
		}

		return $this->getProduct($product_id);
	}

	/**
	 * Получение идентификатора товара по наименованию товара из каталога
	 * - возвращаются простые товары, а так же вариации
	 *
	 * @param string $name
	 *
	 * @return int
	 */
	public function findProductIdByName($name)
	{
		$args =
		[
			'post_type' => ['product', 'product_variation'],
			'post_status' => implode(',', get_post_statuses()),
			'title' => $name,
			'posts_per_page' => -1,
			'fields' => 'ids'
		];

		$posts = get_posts($args);
		$product_id = 0;

		if(is_array($posts) && count($posts) === 1)
		{
			$product_id = reset($posts);
		}

		return $product_id;
	}

	/**
	 * Получение идентификатора(ов) продукта(ов) по идентификатору товара из 1C с возможным указанием характеристики
	 *
	 * @param int|string $external_id Внешний идентификатор продукта
	 * @param int|string $external_characteristic_id Внешний идентификатор характеристики продукта
	 *
	 * @return int|array
	 *
	 * @since 0.8.0
	 */
	public function findIdsByExternalIdAndCharacteristicId($external_id, $external_characteristic_id = '')
	{
		$args =
		[
			'post_type' => ['product', 'product_variation'],
			'post_status' => implode(',', get_post_statuses()),
			'meta_key' => '_wc1c_external_id',
			'meta_value' => $external_id,
			'posts_per_page' => -1,
			'fields' => 'ids',
			'suppress_filters' => true
		];

		if(!empty($external_characteristic_id))
		{
			unset($args['meta_key'], $args['meta_value']);

			$args['meta_query'] =
			[
				'relation' => 'AND',
				[
					'key' => '_wc1c_external_id',
					'value' => $external_id
				],
				[
					'key' => '_wc1c_external_characteristic_id',
					'value' => $external_characteristic_id
				]
			];
		}

		$posts = get_posts($args);
		$product_id = 0;

		if(is_array($posts) && count($posts) === 1)
		{
			$product_id = reset($posts);
		}

		return $product_id;
	}
}