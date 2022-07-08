<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * AttributeContract
 *
 * @package Wc1c\Wc
 */
interface AttributeContract
{
	/**
	 * Установка наименования атрибута в WooCommerce
	 *
	 * @param string $name Наименование атрибута
	 *
	 * @return void
	 */
	public function setName($name);

	/**
	 * Получение наименования атрибута в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование атрибута
	 */
	public function getName($context);

	/**
	 * Установка этикетки атрибута в WooCommerce
	 *
	 * @param string $label Этикетка атрибута
	 *
	 * @return void
	 */
	public function setLabel($label);

	/**
	 * Получение этикетки атрибута в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Этикетка атрибута
	 */
	public function getLabel($context);

	/**
	 * Назначение идентификатора атрибута в 1C
	 *
	 * @param string $id Идентификатор атрибута в 1С
	 *
	 * @return void
	 */
	public function assignExternalId($id);

	/**
	 * Получение идентификации атрибутов в 1C
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string|array|false Идентификатор атрибута в 1С, либо массив идентификаторов. Ложь в случае отсутствия любого значения.
	 */
	public function getExternalId($context = 'view');

	/**
	 * Получение сортировки атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Сортировка атрибута
	 */
	public function getOrder($context);

	/**
	 * Установка сортировки атрибута
	 * Accepts: 'menu_order', 'name', 'name_num' and 'id'. Default to 'menu_order'.
	 *
	 * @param string $label Сортировка атрибута
	 *
	 * @return void
	 */
	public function setOrder($label);

	/**
	 * Получение типа атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Тип атрибута
	 */
	public function getType($context);

	/**
	 * Установка типа атрибута
	 * Core by default accepts: 'select' and 'text'. Default to 'select'.
	 *
	 * @param string $type Тип атрибута
	 *
	 * @return void
	 */
	public function setType($type);

	/**
	 * Получение публичности атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Публичность атрибута
	 */
	public function getPublic($context);

	/**
	 * Установка публичности атрибута
	 * Enable or disable attribute archives. False by default.
	 *
	 * @param string $type Публичность атрибута
	 *
	 * @return void
	 */
	public function setPublic($type);

	/**
	 * Сохранение назначенных данных атрибута в WooCommerce
	 *
	 * @return mixed
	 */
	public function save();

	/**
	 * Удаление атрибута из WooCommerce
	 *
	 * @param boolean $force Окончательное удаление
	 *
	 * @return boolean
	 */
	public function delete($force);

	/**
	 * Установка идентификатора схемы через которую был создан атрибут в WooCommerce
	 *
	 * @param string|int $id Идентификатор схемы
	 *
	 * @return void
	 */
	public function setSchemaId($id);

	/**
	 * Получение идентификатора схемы через которую был создан атрибут в WooCommerce
	 *
	 * @param string|int $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор схемы, либо ложь
	 */
	public function getSchemaId($context);

	/**
	 * Установка идентификатора конфигурации через которую был создан атрибут в WooCommerce
	 *
	 * @param string|int $id Идентификатор конфигурации
	 *
	 * @return void
	 */
	public function setConfigurationId($id);

	/**
	 * Получение идентификатора конфигурации через которую был создан атрибут в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор конфигурации, либо ложь
	 */
	public function getConfigurationId($context);
}