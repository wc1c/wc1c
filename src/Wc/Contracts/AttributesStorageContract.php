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
	 * Получение атрибута или атрибутов по наименованию атрибута из WooCommerce
	 *
	 * @param string $name Наименование искомого атрибута
	 *
	 * @return false|AttributeContract|AttributeContract[]
	 */
	public function getByName($name);

	/**
	 * Получение атрибута или атрибутов по идентификатору атрибута из 1С
	 *
	 * @param $id
	 *
	 * @return false|AttributeContract|AttributeContract[]
	 */
	public function getByExternalId($id);
}