<?php
/**
 * Abstract Extension class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

abstract class Wc1c_Abstract_Extension
{
	/**
	 * Unique ext id
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Extension type
	 *
	 * @var string
	 */
	private $type = '';

	/**
	 * Set ext id
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
	 * Get ext id
	 *
	 * @return string
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_type()
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function set_type($type)
	{
		$this->type = $type;
	}
}