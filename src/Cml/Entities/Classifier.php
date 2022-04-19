<?php namespace Wc1c\Cml\Entities;

defined('ABSPATH') || exit;

use Wc1c\Cml\Abstracts\DataAbstract;
use Wc1c\Cml\Contracts\ClassifierDataContract;
use Wc1c\Cml\Contracts\CounterpartyDataContract;

/**
 * Classifier
 *
 * @package Wc1c\Cml
 */
class Classifier extends DataAbstract implements ClassifierDataContract
{
	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->data['id'];
	}

	/**
	 * @param string $id
	 */
	public function setId($id)
	{
		$this->data['id'] = $id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->data['name'];
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->data['name'] = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->data['description'];
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->data['description'] = $description;
	}

	/**
	 * @return CounterpartyDataContract
	 */
	public function getOwner()
	{
		return $this->data['owner'];
	}

	/**
	 * @param CounterpartyDataContract $owner
	 */
	public function setOwner($owner)
	{
		$this->data['owner'] = $owner;
	}

	/**
	 * @return array
	 */
	public function getGroups()
	{
		return $this->data['groups'];
	}

	/**
	 * @param array $groups
	 */
	public function setGroups($groups)
	{
		$this->data['groups'] = $groups;
	}

	/**
	 * @return mixed
	 */
	public function getProperties()
	{
		return $this->data['properties'];
	}

	/**
	 * @param $properties
	 *
	 * @return void
	 */
	public function setProperties($properties)
	{
		$this->data['properties'] = $properties;
	}

	/**
	 * @return mixed
	 */
	public function getPriceTypes()
	{
		return $this->data['price_types'];
	}

	/**
	 * @param $price_types
	 *
	 * @return void
	 */
	public function setPriceTypes($price_types)
	{
		$this->data['price_types'] = $price_types;
	}
}