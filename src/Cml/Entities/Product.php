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
}