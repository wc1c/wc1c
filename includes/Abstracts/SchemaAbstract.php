<?php
/**
 * Namespace
 */
namespace Wc1c\Abstracts;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Configuration;
use Wc1c\Exceptions\Exception;

/**
 * SchemaAbstract
 *
 * @package Wc1c\Abstracts
 */
abstract class SchemaAbstract
{
	/**
	 * @var bool Initialized flag
	 */
	private $initialized = false;

	/**
	 * @var string Unique schema id
	 */
	private $id = '';

	/**
	 * @var Configuration Current configuration
	 */
	private $configuration = null;

	/**
	 * @var array Unique schema options
	 */
	private $options = [];

	/**
	 * @var string Unique prefix wc1c_prefix_{schema_id}_{configuration_id}
	 */
	private $prefix = '';

	/**
	 * @var string Unique configuration prefix wc1c_configuration_{configuration_id}
	 */
	private $configuration_prefix = '';

	/**
	 * @var string Unique schema prefix wc1c_schema_{schema_id}
	 */
	private $schema_prefix = '';

	/**
	 * @var string Current version
	 */
	private $version = '';

	/**
	 * @var string Name
	 */
	private $name = '';

	/**
	 * @var string Description
	 */
	private $description = '';

	/**
	 * @var string Schema Author
	 */
	private $author = 'WC1C team';

	/**
	 * SchemaAbstract constructor.
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
	public function isInitialized()
	{
		return $this->initialized;
	}

	/**
	 * @param bool $initialized
	 */
	public function setInitialized($initialized)
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
	public function setId($id)
	{
		$this->id = $id;

		return $this;
	}

	/**
	 * Get schema id
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getName()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name)
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription()
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription($description)
	{
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getAuthor()
	{
		return $this->author;
	}

	/**
	 * @param string $author
	 */
	public function setAuthor($author)
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
	public function setOptions($options)
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
	public function getOptions($key = '', $default = null)
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
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @param string $version
	 */
	public function setVersion($version)
	{
		$this->version = $version;
	}

	/**
	 * @return string
	 */
	public function getPrefix()
	{
		return $this->prefix;
	}

	/**
	 * @param string $prefix
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * @return string
	 */
	public function getConfigurationPrefix()
	{
		return $this->configuration_prefix;
	}

	/**
	 * @param string $configuration_prefix
	 */
	public function setConfigurationPrefix($configuration_prefix)
	{
		$this->configuration_prefix = $configuration_prefix;
	}

	/**
	 * @return string
	 */
	public function getSchemaPrefix()
	{
		return $this->schema_prefix;
	}

	/**
	 * @param string $schema_prefix
	 */
	public function setSchemaPrefix($schema_prefix)
	{
		$this->schema_prefix = $schema_prefix;
	}

	/**
	 * @return Configuration
	 */
	public function configuration()
	{
		return $this->configuration;
	}

	/**
	 * @param Configuration|null $configuration
	 */
	public function setConfiguration($configuration)
	{
		$this->configuration = $configuration;
	}
}