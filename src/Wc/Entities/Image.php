<?php namespace Wc1c\Wc\Entities;

defined('ABSPATH') || exit;

use Wc1c\Wc\Abstracts\ImagesData;
use Wc1c\Wc\Contracts\ImageContract;

/**
 * Image
 *
 * @package Wc1c\Wc
 */
class Image extends ImagesData implements ImageContract
{
	/**
	 * @var array Текущие данные изображения
	 */
	protected $data =
	[
		'parent_id' => 0,
		'name' => '',
		'description' => '',
		'user_id' => 1,
		'mime_type' => '',
		'guid' => '',
		'slug' => '',
	];

	/**
	 * Установка наименования
	 *
	 * @param string $name Наименование изображения
	 *
	 * @return void
	 */
	public function setName($name)
	{
		$this->setProp('name', $name);
	}

	/**
	 * Получение наименования изображения
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование изображения
	 */
	public function getName($context = 'view')
	{
		return $this->getProp('name', $context);
	}

	/**
	 * Установка описания изображения
	 *
	 * @param string $description Описание изображения
	 *
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->setProp('description', $description);
	}

	/**
	 * Получение описания изображения
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Описание изображения
	 */
	public function getDescription($context = 'view')
	{
		return $this->getProp('description', $context);
	}

	/**
	 * Установка идентификатора схемы через которую было добавлено изображение в WooCommerce
	 *
	 * @param string|int $id Идентификатор схемы
	 *
	 * @return void
	 */
	public function setSchemaId($id)
	{
		$this->addMetaData('_wc1c_schema_id', $id, true);
	}

	/**
	 * Получение идентификатора схемы через которую было добавлено изображение в WooCommerce
	 *
	 * @param string|int $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор схемы, либо ложь
	 */
	public function getSchemaId($context = 'view')
	{
		$data = $this->getMeta('_wc1c_schema_id', true, $context);

		if(isset($data[0]))
		{
			return $data[0];
		}

		return false;
	}

	/**
	 * Установка идентификатора конфигурации через которую было добавлено изображение в WooCommerce
	 *
	 * @param string|int $id Идентификатор конфигурации
	 *
	 * @return void
	 */
	public function setConfigurationId($id)
	{
		$this->addMetaData('_wc1c_configuration_id', $id, true);
	}

	/**
	 * Получение идентификатора конфигурации через которую было добавлено изображение в WooCommerce
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор конфигурации, либо ложь
	 */
	public function getConfigurationId($context = 'view')
	{
		$data = $this->getMeta('_wc1c_configuration_id', true, $context);

		if(isset($data[0]))
		{
			return $data[0];
		}

		return false;
	}

	/**
	 * Имеет ли категория родителя в WooCommerce
	 *
	 * @return boolean
	 */
	public function hasParent()
	{
		return $this->getParentId() !== 0;
	}

	/**
	 * Получение идентификатора родителя
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return int|mixed Идентификатор родителя
	 */
	public function getParentId($context = 'view')
	{
		return $this->getProp('parent_id', $context);
	}

	/**
	 * Установка идентификатора родителя
	 *
	 * @param int|string $parent_id Идентификатор родителя
	 *
	 * @return void
	 */
	public function setParentId($parent_id)
	{
		$this->setProp('parent_id', $parent_id);
	}

	/**
	 * Установка наименования изображения во внешней системе
	 *
	 * @param string $name Наименование изображения
	 */
	public function setExternalName($name)
	{
		$this->addMetaData('_wc1c_external_name', $name, true);
	}

	/**
	 * Получение наименования изображения во внешней системе
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование изображения
	 */
	public function getExternalName($context = 'view')
	{
		$data = $this->getMeta('_wc1c_external_name', true, $context);

		if(is_array($data) && isset($data[0]))
		{
			return reset($data);
		}

		return false;
	}

	/**
	 * Установка идентификатора пользователя через которого было добавлено изображение
	 *
	 * @param string|int $id Идентификатор пользователя
	 *
	 * @return void
	 */
	public function setUserId($id)
	{
		$this->setProp('user_id', $id);
	}

	/**
	 * Получение идентификатора пользователя через которого было добавлено изображение
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор пользователя, либо ложь
	 */
	public function getUserId($context = 'view')
	{
		return $this->getProp('user_id', $context);
	}

	/**
	 * Установка идентификатора продукта в который было добавлено изображение
	 *
	 * @param string|int $id Идентификатор продукта
	 *
	 * @return void
	 */
	public function setProductId($id)
	{
		$this->setParentId($id);
	}

	/**
	 * Получение идентификатора продукта в который было добавлено изображение
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return false|string|int Идентификатор продукта, либо ложь
	 */
	public function getProductId($context = 'view')
	{
		return $this->getParentId($context);
	}

	/**
	 * Получение типа изображения
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Тип изображения, может быть пустой
	 */
	public function getMimeType($context = 'view')
	{
		return $this->getProp('mime_type', $context);
	}

	/**
	 * Установка типа изображения
	 *
	 * @param string $type Mime type изображения
	 *
	 * @return void
	 */
	public function setMimeType($type)
	{
		$this->setProp('mime_type', $type);
	}

	/**
	 * Установка guid
	 *
	 * @param string $name Наименование guid
	 *
	 * @return void
	 */
	public function setGuid($name)
	{
		$this->setProp('guid', $name);
	}

	/**
	 * Получение наименования guid
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование guid
	 */
	public function getGuid($context = 'view')
	{
		return $this->getProp('guid', $context);
	}

	/**
	 * Установка слага
	 *
	 * @param string $name Наименование слага
	 *
	 * @return void
	 */
	public function setSlug($name)
	{
		$this->setProp('slug', $name);
	}

	/**
	 * Получение слага
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование слага
	 */
	public function getSlug($context = 'view')
	{
		return $this->getProp('slug', $context);
	}
}