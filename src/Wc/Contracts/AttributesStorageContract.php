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
	 * @param string $label Этикетка искомого атрибута
	 *
	 * @return false|AttributeContract
	 */
	public function getByLabel($label);
}