<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * CategoriesStorageContract
 *
 * @package Wc1c\Wc\Contracts
 */
interface CategoriesStorageContract
{
	/**
	 * @param $name
	 *
	 * @return mixed
	 */
	public function getByName($name);

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getById($id);

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function getBy1cId($id);
}