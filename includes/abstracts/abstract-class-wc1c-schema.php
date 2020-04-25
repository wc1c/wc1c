<?php
/**
 * Abstract Schema class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

abstract class Wc1c_Abstract_Schema
{
	/**
	 * Unique schema id
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Unique schema options
	 *
	 * @var array
	 */
	private $options = array();

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
		if($key != '')
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
}