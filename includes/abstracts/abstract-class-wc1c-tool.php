<?php
/**
 * Abstract tool class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

abstract class Abstract_Wc1c_Tool extends Abstract_Wc1c implements Interface_Wc1c_Tool
{
	/**
	 * Unique tool id
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Name
	 *
	 * @var string
	 */
	private $name = '';

	/**
	 * Description
	 *
	 * @var string
	 */
	private $description = '';

	/**
	 * Set tool unique id
	 *
	 * @param $id
	 *
	 * @return Abstract_Wc1c_Tool
	 */
	public function set_id($id)
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Get tool unique id
	 *
	 * @return string
	 */
	public function get_id()
	{
		return $this->id;
	}

	/**
	 * Get tool name
	 *
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * Set tool name
	 *
	 * @param string $name
	 *
	 * @return Abstract_Wc1c_Tool
	 */
	public function set_name($name)
	{
		$this->name = $name;
		return $this;
	}

	/**
	 * Get tool description
	 *
	 * @return string
	 */
	public function get_description()
	{
		return $this->description;
	}

	/**
	 * Set tool description
	 *
	 * @param string $description
	 *
	 * @return Abstract_Wc1c_Tool
	 */
	public function set_description($description)
	{
		$this->description = $description;
		return $this;
	}
}