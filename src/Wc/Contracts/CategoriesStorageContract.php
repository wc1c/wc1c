<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

use Wc1c\Wc\Category;

/**
 * CategoriesStorageContract
 *
 * @package Wc1c\Wc\Contracts
 */
interface CategoriesStorageContract
{
	/**
	 * Получение категории или категорий по наименованию категории из WooCommerce
	 *
	 * @param $name
	 *
	 * @return mixed
	 */
	public function getByName($name);

	/**
	 * Получение категории или категорий по идентификатору категории из WooCommerce
	 *
	 * @param $id
	 *
	 * @return false|Category|Category[]
	 */
	public function getById($id);

	/**
	 * Получение категории или категорий по идентификатору категории из 1С
	 *
	 * @param $id
	 *
	 * @return false|Category|Category[]
	 */
	public function getBy1cId($id);
}