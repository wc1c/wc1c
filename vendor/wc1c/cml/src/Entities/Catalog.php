<?php namespace Wc1c\Cml\Entities;

defined('ABSPATH') || exit;

use Wc1c\Cml\Abstracts\DataAbstract;
use Wc1c\Cml\Contracts\CatalogDataContract;
use Wc1c\Cml\Contracts\CounterpartyDataContract;

/**
 * Catalog
 *
 * @package Wc1c\Cml
 */
class Catalog extends DataAbstract implements CatalogDataContract
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
	public function getClassifierId()
	{
		return $this->data['classifier_id'];
	}

	/**
	 * @param string $id
	 */
	public function setClassifierId($id)
	{
		$this->data['classifier_id'] = $id;
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
	 * @return bool
	 */
	public function isOnlyChanges()
	{
		return $this->data['only_changes'];
	}

	/**
	 * @param bool $only_changes
	 */
	public function setOnlyChanges($only_changes)
	{
		$this->data['only_changes'] = $only_changes;
	}
}