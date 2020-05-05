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
	private $type = 'other';

	/**
	 * All support extension types
	 *
	 * @var array
	 */
	private $types_support = ['schema', 'tool', 'other'];

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

	/**
	 * @return array
	 */
	public function get_types_support()
	{
		return $this->types_support;
	}

	/**
	 * @param array $types_support
	 */
	public function set_types_support($types_support)
	{
		$this->types_support = $types_support;
	}

	/**
	 * @param $type
	 *
	 * @return bool
	 */
	public function is_type_support($type)
	{
		$types = $this->get_types_support();

		return isset($types[$type]);
	}
}