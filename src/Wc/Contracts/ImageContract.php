<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * ImageContract
 *
 * @package Wc1c\Wc
 */
interface ImageContract
{
	/**
	 * Получение уникального идентификатора изображения
	 *
	 * @return integer Идентификатор изображения
	 */
	public function getId();

	/**
	 * Установка идентификатора пользователя через которого было добавлено изображение
	 *
	 * @param string|int $id Идентификатор пользователя
	 *
	 * @return mixed
	 */
	public function setUserId($id);

	/**
	 * Получение идентификатора пользователя через которого было добавлено изображение
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор пользователя, либо ложь
	 */
	public function getUserId($context = 'view');

	/**
	 * Установка наименования изображения в WooCommerce
	 *
	 * @param string $name Наименование изображения
	 */
	public function setName($name);

	/**
	 * Получение наименования изображения в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование изображения
	 */
	public function getName($context = 'view');

	/**
	 * Установка описания изображения в WooCommerce
	 *
	 * @param string $description Описание категории
	 */
	public function setDescription($description);

	/**
	 * Получение описания изображения
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Описание изображения
	 */
	public function getDescription($context = 'view');

	/**
	 * Сохранение назначенных данных изображения в WooCommerce
	 *
	 * @return mixed
	 */
	public function save();

	/**
	 * Удаление изображения из WooCommerce
	 *
	 * @param boolean $force Окончательное удаление
	 *
	 * @return boolean
	 */
	public function delete($force = false);

	/**
	 * Установка идентификатора схемы через которую было добавлено изображение в WooCommerce
	 *
	 * @param string|int $id Идентификатор схемы
	 *
	 * @return void
	 */
	public function setSchemaId($id);

	/**
	 * Получение идентификатора схемы через которую было добавлено изображение в WooCommerce
	 *
	 * @param string|int $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор схемы, либо ложь
	 */
	public function getSchemaId($context = 'view');

	/**
	 * Установка идентификатора конфигурации через которую было добавлено изображение в WooCommerce
	 *
	 * @param string|int $id Идентификатор конфигурации
	 *
	 * @return mixed
	 */
	public function setConfigurationId($id);

	/**
	 * Получение идентификатора конфигурации через которую было добавлено изображение в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор конфигурации, либо ложь
	 */
	public function getConfigurationId($context);

	/**
	 * Установка наименования изображения во внешней системе
	 *
	 * @param string $name Наименование изображения
	 */
	public function setExternalName($name);

	/**
	 * Получение наименования изображения во внешней системе
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование изображения
	 */
	public function getExternalName($context = 'view');

	/**
	 * Установка идентификатора продукта в который было добавлено изображение
	 *
	 * @param string|int $id Идентификатор продукта
	 *
	 * @return void
	 */
	public function setProductId($id);

	/**
	 * Получение идентификатора продукта в который было добавлено изображение
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор продукта, либо ложь
	 */
	public function getProductId($context = 'view');

	/**
	 * Получение типа изображения
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Тип изображения, может быть пустой
	 */
	public function getMimeType($context = 'view');

	/**
	 * Установка типа изображения
	 *
	 * @param string $type Mime type изображения
	 *
	 * @return void
	 */
	public function setMimeType($type);
}