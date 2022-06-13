<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * CategoriesStorageContract
 *
 * @package Wc1c\Wc
 */
interface CategoriesStorageContract
{
	/**
	 * Получение категории или категорий по наименованию категории из WooCommerce
	 *
	 * @param string $name Наименование искомой категории
	 *
	 * @return false|CategoryContract|CategoryContract[]
	 */
	public function getByName($name);

	/**
	 * Получение категории по идентификатору категории из WooCommerce
	 *
	 * @param int|string $id Идентификатор категории
	 *
	 * @return false|CategoryContract
	 */
	public function getById($id);

	/**
	 * Получение категории или категорий по идентификатору категории из 1С
	 *
	 * @param int|string $id
	 *
	 * @return false|CategoryContract|CategoryContract[]
	 */
	public function getByExternalId($id);
}