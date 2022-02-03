<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * ClassifierDataContract
 *
 * @package Wc1c\Cml\Contracts
 */
interface ClassifierDataContract extends DataContract
{
	/**
	 * @return string
	 */
	public function getId();

	/**
	 * @return string
	 */
	public function getName();

	/**
	 * @return string
	 */
	public function getDescription();

	/**
	 * @return CounterpartyDataContract
	 */
	public function getOwner();

	/**
	 * @return mixed
	 */
	public function getGroups();

	/**
	 * @return mixed
	 */
	public function getProperties();

	/**
	 * @return mixed
	 */
	public function getPriceTypes();
}