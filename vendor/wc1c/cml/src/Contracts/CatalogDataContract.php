<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * CatalogDataContract
 *
 * @package Wc1c\Cml
 */
interface CatalogDataContract extends DataContract
{
	/**
	 * @return string Unique identifier
	 */
	public function getId(): string;

	/**
	 * @return string Classifier identifier
	 */
	public function getClassifierId(): string;

	/**
	 * @return string Catalog name
	 */
	public function getName(): string;

	/**
	 * @return CounterpartyDataContract Catalog owner
	 */
	public function getOwner(): CounterpartyDataContract;
}