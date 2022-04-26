<?php namespace Wc1c\Wc;

defined('ABSPATH') || exit;

use Wc1c\Wc\Contracts\CategoryContract;
use Wc1c\Wc\Entities\CategoriesData;

/**
 * Category
 *
 * @package Wc1c\Wc
 */
class Category extends CategoriesData implements CategoryContract
{
	/**
	 * Текущие данные
	 *
	 * @var array
	 */
	protected $data =
	[
		'parent_id' => 0,
		'name' => '',
		'description' => '',
		'slug' => '',
		'image_id' => 0,
		'display_type' => '',
	];

	/**
	 * Установка имени
	 *
	 * @param $name
	 *
	 * @return void
	 */
	public function setName($name)
	{
		$this->setProp('name', $name);
	}

	/**
	 * Получение имени
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function getName($context = 'view')
	{
		return $this->getProp('name', $context);
	}

	/**
	 * Получение слага
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function getSlug($context = 'view')
	{
		return $this->getProp('slug', $context);
	}

	/**
	 * Установка слага
	 *
	 * @param $slug
	 *
	 * @return void
	 */
	public function setSlug($slug)
	{
		$this->setProp('slug', $slug);
	}

	/**
	 * Установка описания
	 *
	 * @param $description
	 *
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->setProp('description', $description);
	}

	/**
	 * Получение описания
	 *
	 * @param string $context
	 *
	 * @return string
	 */
	public function getDescription($context = 'view')
	{
		return $this->getProp('description', $context);
	}

	/**
	 * Установка идентификатора схемы через которую была создана категория
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
	 * Получение идентификатора схемы через которую была создана категория
	 *
	 * @param $context
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
	 * Установка идентификатора конфигурации через которую создана категория
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
	 * Получение идентификатора конфигурации через которую была создана категория
	 *
	 * @param $context
	 *
	 * @return string|int|false Идентификатор конфигурации или false
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
	 * Назначение идентификатора категории из 1С
	 *
	 * @param $id
	 *
	 * @return void
	 */
	public function assign1cId($id)
	{
		$this->addMetaData('_wc1c_1c_id', $id, false);
	}

	/**
	 * Получение идентификаторов категории назначенных из 1С
	 *
	 * @param $context
	 *
	 * @return array|string
	 */
	public function get1cId($context = 'view')
	{
		return $this->getMeta('_wc1c_1c_id', true, $context);
	}

	/**
	 * Имеет ли категория родителя
	 *
	 * @return boolean
	 */
	public function hasParent()
	{
		return $this->getParentId() !== 0;
	}

	/**
	 * Получение идентификатора категории родителя
	 *
	 * @param $context
	 *
	 * @return int|mixed
	 */
	public function getParentId($context = 'view')
	{
		return $this->getProp('parent_id', $context);
	}

	/**
	 * Установка идентификаторка категории родителя
	 *
	 * @param $parent_id
	 *
	 * @return void
	 */
	public function setParentId($parent_id)
	{
		$this->setProp('parent_id', $parent_id);
	}

	/**
	 * @param $context
	 *
	 * @return array|int|mixed|string
	 */
	public function get1cParentId($context = 'view')
	{
		return $this->getMeta('_wc1c_1c_parent_id', true, $context);
	}

	/**
	 * Назначение идентификатора родительской категории в 1С
	 *
	 * @param $id
	 *
	 * @return void
	 */
	public function assign1cParentId($id)
	{
		$this->addMetaData('_wc1c_1c_parent_id', $id, false);
	}

	/**
	 * Имеет ли категория в 1С родителюскую категорию
	 *
	 * @return bool
	 */
	public function has1cParent()
	{
		return $this->get1cParentId('view') !== 0;
	}
}