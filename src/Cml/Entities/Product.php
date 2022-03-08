<?php namespace Wc1c\Cml\Entities;

defined('ABSPATH') || exit;

use Wc1c\Cml\Abstracts\DataAbstract;
use Wc1c\Cml\Contracts\ProductDataContract;

/**
 * Product
 *
 * @package Wc1c\Cml\Entities
 */
class Product extends DataAbstract implements ProductDataContract
{
	/**
	 * @return string|false
	 */
	public function getId()
	{
		if(!isset($this->data['id']))
		{
			return false;
		}

		return $this->data['id'];
	}

	/**
	 * @return string|false
	 */
	public function getSku()
	{
		if(!isset($this->data['sku']))
		{
			return false;
		}

		return $this->data['sku'];
	}

	/**
	 * @param $id
	 *
	 * @return mixed
	 */
	public function setId($id)
	{
		$this->data['id'] = $id;

		return $this->data['id'];
	}

	/**
	 * @return false|string
	 */
	public function getName()
	{
		if(!isset($this->data['name']))
		{
			return false;
		}

		return $this->data['name'];
	}

	/**
	 * @param $name
	 *
	 * @return string
	 */
	public function setName($name)
	{
		$this->data['name'] = $name;

		return $this->data['name'];
	}

	/**
	 * @return false|mixed
	 */
	public function getDescription()
	{
		if(!isset($this->data['description']))
		{
			return false;
		}

		return $this->data['description'];
	}

	/**
	 * @return false|mixed
	 */
	public function getRequisites()
	{
		if(!isset($this->data['requisites']))
		{
			return false;
		}

		return $this->data['requisites'];
	}

	/**
	 * @return false|mixed
	 */
	public function getPropertyValues()
	{
		if(!isset($this->data['property_values']))
		{
			return false;
		}

		return $this->data['property_values'];
	}

	/**
	 * @return false|array
	 */
	public function getImages()
	{
		if(!isset($this->data['images']))
		{
			return false;
		}

		return $this->data['images'];
	}

	/**
	 * @return false|array
	 */
	public function getPrices()
	{
		if(!isset($this->data['prices']))
		{
			return false;
		}

		return $this->data['prices'];
	}

	/**
	 * @return int|string
	 */
	public function getFeatureId()
	{
		if(!isset($this->data['feature_id']))
		{
			return '';
		}

		return $this->data['feature_id'];
	}

	/**
	 * @return bool
	 */
	public function hasFeatureId()
	{
		return $this->getFeatureId() !== '';
	}

	/**
	 * @return bool
	 */
	public function hasClassifierGroups()
	{
		if(empty($this->data['classifier_groups']))
		{
			return false;
		}

		return true;
	}

	/**
	 * @return array
	 */
	public function getClassifierGroups()
	{
		if(empty($this->data['classifier_groups']))
		{
			return [];
		}

		return $this->data['classifier_groups'];
	}
}