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
	 * Current version
	 *
	 * @var string
	 */
	private $version = '';

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
	
	/**
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function set_name($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_description()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function set_description($description)
	{
		$this->description = $description;
	}
	
	/**
	 * @return string
	 */
	public function get_version()
	{
		return $this->version;
	}

	/**
	 * @param string $version
	 */
	public function set_version($version)
	{
		$this->version = $version;
	}
}