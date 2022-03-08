<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * ProductDataContract
 *
 * @package Wc1c\Cml\Contracts
 */
interface ProductDataContract extends DataContract
{
	/**
	 * @return string Unique product id in Catalog
	 */
	public function getId();

	/**
	 * @return string Unique product feature id in Catalog
	 */
	public function getFeatureId();

	/**
	 * @return boolean
	 */
	public function hasFeatureId();

	/**
	 * @return string Product name
	 */
	public function getName();

	/**
	 * @return string Product description
	 */
	public function getDescription();

	/**
	 * @return boolean
	 */
	public function hasClassifierGroups();

	/**
	 * @return array Product groups from Classifier
	 */
	public function getClassifierGroups();
}