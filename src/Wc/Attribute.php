<?php namespace Wc1c\Wc;

defined('ABSPATH') || exit;

use Wc1c\Wc\Abstracts\AttributesData;
use Wc1c\Wc\Contracts\AttributeContract;

/**
 * Attribute
 *
 * @package Wc1c\Wc
 */
class Attribute extends AttributesData implements AttributeContract
{
	/**
	 * @var array Текущие данные
	 */
	protected $data =
	[
		'name' => '',
		'label' => '',
		'type' => 'select',
		'public' => 0,
		'order' => 'menu_order'
	];

	/**
	 * Установка наименования
	 *
	 * @param string $name Наименование атрибута
	 *
	 * @return void
	 */
	public function setName($name)
	{
		$this->setProp('name', $name);
	}

	/**
	 * Получение наименования атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Наименование атрибута
	 */
	public function getName($context = 'view')
	{
		return $this->getProp('name', $context);
	}

	/**
	 * Получение этикетки атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Этикетка атрибута
	 */
	public function getLabel($context = 'view')
	{
		return $this->getProp('label', $context);
	}

	/**
	 * Установка этикетки атрибута
	 *
	 * @param string $label Этикетка атрибута
	 *
	 * @return void
	 */
	public function setLabel($label)
	{
		$this->setProp('label', $label);
	}

	/**
	 * Получение сортировки атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Сортировка атрибута
	 */
	public function getOrder($context = 'view')
	{
		return $this->getProp('order', $context);
	}

	/**
	 * Установка сортировки атрибута
	 *
	 * @param string $label Сортировка атрибута
	 *
	 * @return void
	 */
	public function setOrder($label)
	{
		$this->setProp('order', $label);
	}

	/**
	 * Получение типа атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Тип атрибута
	 */
	public function getType($context = 'view')
	{
		return $this->getProp('type', $context);
	}

	/**
	 * Установка типа атрибута
	 *
	 * @param string $type Тип атрибута
	 *
	 * @return void
	 */
	public function setType($type)
	{
		$this->setProp('type', $type);
	}

	/**
	 * Получение публичности атрибута
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string Публичность атрибута
	 */
	public function getPublic($context = 'view')
	{
		return $this->getProp('public', $context);
	}

	/**
	 * Установка публичности атрибута
	 *
	 * @param string $type Публичность атрибута
	 *
	 * @return void
	 */
	public function setPublic($type)
	{
		$this->setProp('public', $type);
	}

	/**
	 * Установка идентификатора схемы через которую был создан атрибут
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
	 * Получение идентификатора схемы через которую был создан атрибут
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string|int|false Идентификатор схемы или false
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
	 * Установка идентификатора конфигурации через которую был создан атрибут
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
	 * Получение идентификатора конфигурации через которую был создан атрибут
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string|int|false Идентификатор конфигурации или ложь
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
	 * Назначение идентификатора атрибута из 1С
	 *
	 * @param string|int $id Идентификатор атрибута в 1С
	 *
	 * @return void
	 */
	public function assignExternalId($id)
	{
		$this->addMetaData('_wc1c_external_id', $id, false);
	}

	/**
	 * Получение идентификации атрибутов в 1C
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string|array|false Идентификатор атрибута в 1С, либо массив идентификаторов. Ложь в случае отсутствия любого значения.
	 */
	public function getExternalId($context = 'view')
	{
		$data = $this->getMeta('_wc1c_external_id', true, $context);

		if(is_array($data) && isset($data[0]))
		{
			if(count($data) === 1)
			{
				return $data[0];
			}

			return $data;
		}

		return false;
	}

	/**
	 * Get a product attribute ID by name.
	 *
	 * @param string $name Attribute name.
	 *
	 * @return int
	 */
	public function getTaxonomyIdByName($name = '')
	{
		if(empty($name))
		{
			$name = $this->getName();
		}

		return wc_attribute_taxonomy_id_by_name($name);
	}

	/**
	 * Get a product attribute name.
	 *
	 * @param string $attribute_name Attribute name.
	 *
	 * @return string
	 */
	public function getTaxonomyName($attribute_name = '')
	{
		if(empty($attribute_name))
		{
			$attribute_name = $this->getName();
		}

		return $attribute_name ? 'pa_' . wc_sanitize_taxonomy_name($attribute_name) : '';
	}
}