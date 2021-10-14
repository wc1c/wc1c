<?php
/**
 * Abstract Schema class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

abstract class Abstract_Wc1c_Schema
{
	/**
	 * Extension initialized flag
	 *
	 * @var bool
	 */
	private $initialized = false;

	/**
	 * Unique schema id
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Current configuration
	 *
	 * @var Wc1c_Configuration
	 */
	private $configuration = null;

	/**
	 * Unique schema options
	 *
	 * @var array
	 */
	private $options = [];

	/**
	 * Unique prefix
	 *
	 * wc1c_prefix_{schema_id}_{config_id}
	 *
	 * @var string
	 */
	private $prefix = '';

	/**
	 * Unique configuration prefix
	 *
	 * wc1c_configuration_{config_id}
	 *
	 * @var string
	 */
	private $configuration_prefix = '';

	/**
	 * Unique schema prefix
	 *
	 * wc1c_schema_{schema_id}
	 *
	 * @var string
	 */
	private $schema_prefix = '';

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
	 * @var string
	 */
	private $author = 'WC1C team';

	/**
	 * Wc1c_Abstract_Schema constructor
	 */
	public function __construct(){}

	/**
	 * @throws Exception
	 *
	 * @return mixed
	 */
	abstract public function init();

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
	 * Set schema id
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
	 * Get schema id
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
	public function get_author()
	{
		return $this->author;
	}

	/**
	 * @param string $author
	 */
	public function set_author($author)
	{
		$this->author = $author;
	}

	/**
	 * Set schema options
	 *
	 * @param $options
	 *
	 * @return $this
	 */
	public function set_options($options)
	{
		$this->options = $options;

		return $this;
	}

	/**
	 * Get schema options
	 *
	 * @param string $key - unique option id
	 * @param null $default - false for error
	 *
	 * @return array|bool|null
	 */
	public function get_options($key = '', $default = null)
	{
		if($key !== '')
		{
			if(is_array($this->options) && array_key_exists($key, $this->options))
			{
				return $this->options[$key];
			}

			if(false === is_null($default))
			{
				return $default;
			}

			return false;
		}

		return $this->options;
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
	public function get_prefix()
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function set_prefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public function get_configuration_prefix()
	{
		return $this->configuration_prefix;
	}

	/**
	 * @param string $configuration_prefix
	 */
	public function set_configuration_prefix($configuration_prefix)
	{
		$this->configuration_prefix = $configuration_prefix;
	}

	/**
	 * @return string
	 */
	public function get_schema_prefix()
	{
		return $this->schema_prefix;
	}

	/**
	 * @param string $schema_prefix
	 */
	public function set_schema_prefix($schema_prefix)
	{
		$this->schema_prefix = $schema_prefix;
	}

	/**
	 * @return Wc1c_Configuration|null
	 */
	public function configuration()
	{
		return $this->configuration;
	}

	/**
	 * @param Wc1c_Configuration|null $configuration
	 */
	public function set_configuration($configuration)
	{
		$this->configuration = $configuration;
	}
}