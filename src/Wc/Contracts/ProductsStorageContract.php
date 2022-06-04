<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * ProductsStorageContract
 *
 * @package Wc1c\Wc
 */
interface ProductsStorageContract
{
	/**
	 * Получение продукта или продуктов по наименованию продукта из WooCommerce
	 *
	 * @param string $name Наименование искомого продукта
	 *
	 * @return false|ProductContract|ProductContract[]
	 */
	public function getByName($name);

	/**
	 * Получение продукта или продуктов по идентификатору продукта из 1С
	 *
	 * @param $id
	 *
	 * @return false|ProductContract|ProductContract[]
	 */
	public function getByExternalId($id);
}