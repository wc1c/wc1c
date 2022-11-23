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
	public function getId(): string
	{
		return $this->data['id'];
	}

	/**
	 * @param string $id
	 */
	public function setId(string $id)
	{
		$this->data['id'] = $id;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->data['name'];
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name)
	{
		$this->data['name'] = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->data['description'];
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description)
	{
		$this->data['description'] = $description;
	}

	/**
	 * @return CounterpartyDataContract
	 */
	public function getOwner(): CounterpartyDataContract
	{
		return $this->data['owner'];
	}

	/**
	 * @param CounterpartyDataContract $owner
	 */
	public function setOwner(CounterpartyDataContract $owner)
	{
		$this->data['owner'] = $owner;
	}

	/**
	 * @return array
	 */
	public function getGroups(): array
	{
		return $this->data['groups'];
	}

	/**
	 * @param array $groups
	 */
	public function setGroups(array $groups)
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