<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * ClassifierDataContract
 *
 * @package Wc1c\Cml
 */
interface ClassifierDataContract extends DataContract
{
	/**
	 * @return string Unique id
	 */
	public function getId();

	/**
	 * @return string Classifier name
	 */
	public function getName();

	/**
	 * @return string Classifier description
	 */
	public function getDescription();

	/**
	 * @return CounterpartyDataContract Classifier owner
	 */
	public function getOwner();

	/**
	 * @return array Classifier groups
	 */
	public function getGroups();

	/**
	 * @return mixed Classifier properties
	 */
	public function getProperties();

	/**
	 * @return mixed Classifier price types
	 */
	public function getPriceTypes();
}