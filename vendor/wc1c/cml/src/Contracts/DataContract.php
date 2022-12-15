<?php namespace Wc1c\Cml\Contracts;

/**
 * DataContract
 *
 * @package Wc1c\Cml
 */
interface DataContract
{
	/**
	 * @return array Raw data
	 */
	public function getData(): array;
}