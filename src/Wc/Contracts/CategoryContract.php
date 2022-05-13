<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * CategoryContract
 *
 * @package Wc1c\Wc
 */
interface CategoryContract
{
	/**
	 * Получение идентификатора категории в WooCommerce
	 *
	 * @return integer Идентификатор категории в WooCommerce
	 */
	public function getId();

	/**
	 * Получение идентификатора родительской категории в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Идентификатор родительской категории в WooCommerce
	 */
	public function getParentId($context);

	/**
	 * Получение идентификации категорий в 1C
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string|array|false Идентификатор категории в 1с, либо массив идентификаторов. Ложь в случае отсутствия любого значения.
	 */
	public function getExternalId($context);

	/**
	 * Получение идентификации родительских категорий в 1C
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string|array|false Идентификатор родительской категории в 1с, либо массив идентификаторов. Ложь в случае отсутствия любого значения.
	 */
	public function getExternalParentId($context);

	/**
	 * Установка наименования категории в WooCommerce
	 *
	 * @param string $name Наименование категории
	 */
	public function setName($name);

	/**
	 * Получение наименования категории в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование категории
	 */
	public function getName($context);

	/**
	 * Установка описания категории в WooCommerce
	 *
	 * @param string $description Описание категории
	 */
	public function setDescription($description);

	/**
	 * Получение описания категории
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Описание категории
	 */
	public function getDescription($context);

	/**
	 * Назначение идентификатора категории в 1C
	 *
	 * @param string $id Идентификатор категории в 1С
	 *
	 * @return void
	 */
	public function assignExternalId($id);

	/**
	 * Назначение родительского идентификатора категории в 1C
	 *
	 * @param string|int $id Идентификатор родительской категории в 1С
	 *
	 * @return void
	 */
	public function assignExternalParentId($id);

	/**
	 * Сохранение назначенных данных категории в WooCommerce
	 *
	 * @return mixed
	 */
	public function save();

	/**
	 * Удаление категории из WooCommerce
	 *
	 * @param boolean $force Окончательное удаление
	 *
	 * @return boolean
	 */
	public function delete($force);

	/**
	 * Установка идентификатора схемы через которую была создана категория в WooCommerce
	 *
	 * @param string|int $id Идентификатор схемы
	 *
	 * @return void
	 */
	public function setSchemaId($id);

	/**
	 * Получение идентификатора схемы через которую была создана категория в WooCommerce
	 *
	 * @param string|int $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор схемы, либо ложь
	 */
	public function getSchemaId($context);

	/**
	 * Установка идентификатора конфигурации через которую была создана категория в WooCommerce
	 *
	 * @param string|int $id Идентификатор конфигурации
	 *
	 * @return mixed
	 */
	public function setConfigurationId($id);

	/**
	 * Получение идентификатора конфигурации через которую была создана категория в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор конфигурации, либо ложь
	 */
	public function getConfigurationId($context);

	/**
	 * Имеет ли категория родителя в WooCommerce
	 *
	 * @return boolean
	 */
	public function hasParent();

	/**
	 * Имеет ли категория родителя в 1С, при этом родителей может быть множество
	 *
	 * @return boolean
	 */
	public function hasExternalParent();
}