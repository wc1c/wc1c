<?php
/**
 * Abstract tool class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

abstract class Wc1c_Abstract_Tool
{
	/**
	 * Unique tool id
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Set tool id
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function set_id($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Get tool id
	 *
	 * @return string
	 */
	public function get_id()
	{
		return $this->id;
	}
}