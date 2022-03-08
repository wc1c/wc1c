<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * CatalogDataContract
 *
 * @package Wc1c\Cml\Contracts
 */
interface CatalogDataContract extends DataContract
{
	/**
	 * @return string Unique identifier
	 */
	public function getId();

	/**
	 * @return string Classifier identifier
	 */
	public function getClassifierId();

	/**
	 * @return string Catalog name
	 */
	public function getName();

	/**
	 * @return CounterpartyDataContract Catalog owner
	 */
	public function getOwner();
}