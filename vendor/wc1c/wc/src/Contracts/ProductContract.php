<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;

/**
 * ProductContract
 *
 * @package Wc1c\Wc
 */
interface ProductContract
{
	/**
	 * Получение идентификатора продукта в WooCommerce
	 *
	 * @return integer
	 */
	public function getId();

	/**
	 * Сохранение продукта в WooCommerce (создание не существующего, либо обновление существующего)
	 *
	 * @return int Идентификатор продукта в WooCommerce
	 */
	public function save();

	/**
	 * Установка идентификатора схемы через которую создается продукт
	 *
	 * @param string|int $id Идентификатор схемы
	 *
	 * @return void
	 */
	public function setSchemaId($id);

	/**
	 * Получение идентификатора схемы через которую был создан продукт
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return int|string Идентификатор схемы
	 */
	public function getSchemaId($context);

	/**
	 * Установка идентификатора конфигурации
	 *
	 * @param string|int $id Идентификатор конфигурации
	 *
	 * @return void
	 */
	public function setConfigurationId($id);

	/**
	 * Получение идентификатора конфигурации через которую был создан продукт
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return int|string Идентификатор конфигурации
	 */
	public function getConfigurationId($context);

	/**
	 * Установка артикула продукта с опциональным учетом уникальности
	 *
	 * @param string $sku Артикул продукта
	 * @param boolean $unique Требовать ли уникальное значение
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setSku($sku, $unique = false);

	/**
	 * Получение текущего артикула продукта
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string
	 */
	public function getSku($context);
}