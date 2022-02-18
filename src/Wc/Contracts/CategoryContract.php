<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * CategoryContract
 *
 * @package Wc1c\Wc\Contracts
 */
interface CategoryContract
{
	/**
	 * Идентификатор категории в WooCommerce
	 *
	 * @return string|integer
	 */
	public function getId();

	/**
	 * Идентификатор родительской категории в WooCommerce
	 *
	 * @param string $context
	 *
	 * @return string|integer
	 */
	public function getParentId($context);

	/**
	 * Идентификатор категории в 1C
	 *
	 * @param string $context
	 *
	 * @return string|integer
	 */
	public function get1cId($context);

	/**
	 * Идентификатор родительской категории в 1C
	 *
	 * @param string $context
	 *
	 * @return string|integer
	 */
	public function get1cParentId($context);

	/**
	 * Установка имени категории
	 *
	 * @param string $name
	 */
	public function setName($name);

	/**
	 * Получение имени категории
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function getName($context);

	/**
	 * Установка описания категории
	 *
	 * @param string $description
	 */
	public function setDescription($description);

	/**
	 * Получение описания категории
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function getDescription($context);

	/**
	 * Установка идентификатора категории в 1C
	 *
	 * @return string|integer|false
	 */
	public function set1cId($id);

	/**
	 * Установка родительского идентификатора категории в 1C
	 *
	 * @return string|integer|false
	 */
	public function set1cParentId($id);

	/**
	 * Сохранение категории в WooCommerce
	 *
	 * @return mixed
	 */
	public function save();

	/**
	 * Удаление категории в WooCommerce
	 *
	 * @param boolean $force
	 *
	 * @return boolean
	 */
	public function delete($force);

	/**
	 * Установка идентификатора схемы
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function setSchemaId($id);

	/**
	 * Получение идентификатора схемы
	 *
	 * @return mixed
	 */
	public function getSchemaId($context);

	/**
	 * Установка идентификатора конфигурации
	 *
	 * @param $id
	 *
	 * @return mixed
	 */
	public function setConfigurationId($id);

	/**
	 * Получение идентификатора конфигурации
	 *
	 * @param $context
	 *
	 * @return mixed
	 */
	public function getConfigurationId($context);

	/**
	 * Имеет ли категория родителя
	 *
	 * @return boolean
	 */
	public function hasParent();

	/**
	 * Имеет ли категория родителя в 1С
	 *
	 * @return boolean
	 */
	public function has1cParent();
}