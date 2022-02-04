<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * ProductDataContract
 *
 * @package Wc1c\Cml\Contracts
 */
interface ProductDataContract extends DataContract
{
	public function getId();

	public function getName();

	public function getDescription();
}