<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * AttributesStorageContract
 *
 * @package Wc1c\Wc
 */
interface AttributesStorageContract
{
	/**
	 * Получение атрибута по наименованию атрибута из WooCommerce
	 *
	 * @param string $name Наименование искомого атрибута
	 *
	 * @return false|AttributeContract
	 */
	public function getByName($name);

	/**
	 * Получение атрибута по этикетке атрибута из WooCommerce
	 *
	 * @param string $name Этикетка искомого атрибута
	 *
	 * @return false|AttributeContract
	 */
	public function getByLabel($name);

	/**
	 * Получение атрибута по идентификатору атрибута из 1С
	 *
	 * @param string $id Идентификатор атрибута в 1С
	 *
	 * @return false|AttributeContract
	 */
	public function getByExternalId($id);
}