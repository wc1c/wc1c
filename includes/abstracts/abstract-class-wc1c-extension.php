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
	 * Extension initialized flag
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * Unique id
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
	 * Extension type
	 *
	 * @var string
	 */
	private $type = 'other';

	/**
	 * All support types
	 *
	 * @var array
	 */
	private $types_support = [
		'schema',
		'tool',
		'other'
	];

	/**
	 * Available area
	 *
	 * @var array
	 */
	private $areas = ['any'];

	/**
	 * All support areas
	 *
	 * @var array
	 */
	private $areas_support = [
		'admin',
		'site',
		'wc1c_admin',
		'wc1c_api',
		'any'
	];

	/**
	 * Wc1c_Abstract_Extension constructor
	 */
	public function __construct(){}

	/**
	 * @throws Exception
	 *
	 * @return mixed
	 */
	abstract function init();

	/**
	 * @return bool
	 */
	public function is_initialized()
	{
		return $this->initialized;
	}

	/**
	 * @param bool $initialized
	 */
	public function set_initialized($initialized)
	{
		$this->initialized = $initialized;
	}

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

	/**
	 * @return array
	 */
	public function get_areas()
	{
		return $this->areas;
	}

	/**
	 * @param array $areas
	 */
	public function set_areas($areas)
	{
		$this->areas = $areas;
	}

	/**
	 * @return array
	 */
	public function get_areas_support()
	{
		return $this->areas_support;
	}

	/**
	 * @param array $areas_support
	 */
	public function set_areas_support($areas_support)
	{
		$this->areas_support = $areas_support;
	}
}