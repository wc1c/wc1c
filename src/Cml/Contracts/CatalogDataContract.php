<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * CatalogDataContract
 *
 * @package Wc1c\Cml\Contracts
 */
interface CatalogDataContract extends DataContract
{
	public function getId();

	public function getClassifierId();

	public function getName();

	public function getOwner();
}