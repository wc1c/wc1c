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
	 * @param $name
	 *
	 * @return void
	 */
	public function setName($name)
	{
		$this->setProp('name', $name);
	}

	/**
	 * @return string
	 */
	public function getName($context = 'view')
	{
		return $this->getProp('name', $context);
	}

	/**
	 * @return string
	 */
	public function getSlug($context = 'view')
	{
		return $this->getProp('slug', $context);
	}

	/**
	 * @param $slug
	 *
	 * @return void
	 */
	public function setSlug($slug)
	{
		$this->setProp('slug', $slug);
	}

	/**
	 * @param $description
	 *
	 * @return void
	 */
	public function setDescription($description)
	{
		$this->setProp('description', $description);
	}

	/**
	 * @return string
	 */
	public function getDescription($context = 'view')
	{
		return $this->getProp('description', $context);
	}

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function setSchemaId($id)
	{
		$this->addMetaData('_wc1c_schema_id', $id, true);
	}

	/**
	 * @param $context
	 *
	 * @return array|string
	 */
	public function getSchemaId($context = 'view')
	{
		return $this->getMeta('_wc1c_schema_id', true, $context);
	}

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function setConfigurationId($id)
	{
		$this->addMetaData('_wc1c_configuration_id', $id, true);
	}

	/**
	 * @param $context
	 *
	 * @return array|string
	 */
	public function getConfigurationId($context = 'view')
	{
		return $this->getMeta('_wc1c_configuration_id', true, $context);
	}

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function assign1cId($id)
	{
		$this->addMetaData('_wc1c_1c_id', $id, false);
	}

	/**
	 * @param $context
	 *
	 * @return array|string
	 */
	public function get1cId($context = 'view')
	{
		return $this->getMeta('_wc1c_1c_id', false, $context);
	}

	/**
	 * Имеет ли категория родителя
	 *
	 * @return boolean
	 */
	public function hasParent()
	{
		return $this->getProp('parent_id', 'view') !== 0;
	}

	/**
	 * @param $context
	 *
	 * @return int|mixed
	 */
	public function getParentId($context)
	{
		return $this->getProp('parent_id', $context);
	}

	/**
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
	public function get1cParentId($context)
	{
		return $this->getMeta('_wc1c_1c_parent_id', false, $context);
	}

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function assign1cParentId($id)
	{
		$this->addMetaData('_wc1c_1c_parent_id', $id, false);
	}

	/**
	 * @return bool
	 */
	public function has1cParent()
	{
		return $this->get1cParentId('view') !== 0;
	}
}