<?php namespace Wc1c\Cml\Contracts;

defined('ABSPATH') || exit;

/**
 * CounterpartyDataContract
 *
 * @package Wc1c\Cml\Contracts
 */
interface CounterpartyDataContract extends DataContract
{
	public function getId();

	public function getName();
}