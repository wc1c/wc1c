<?php namespace Wc1c\Cml\Entities;

defined('ABSPATH') || exit;

use Wc1c\Cml\Abstracts\DataAbstract;
use Wc1c\Cml\Contracts\CounterpartyDataContract;

/**
 * Counterparty
 *
 * @package Wc1c\Cml
 */
class Counterparty extends DataAbstract implements CounterpartyDataContract
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
}