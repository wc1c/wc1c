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
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;

/**
 * Class ExtensionAbstract
 *
 * @package Wc1c\Abstracts
 */
abstract class ExtensionAbstract
{
	/**
	 * @var string Unique id
	 */
	private $id = '';

	/**
	 * @var array
	 */
	public $meta = [];

	/**
	 * @var bool Extension initialized flag
	 */
	private $initialized = false;

	/**
	 * ExtensionAbstract constructor.
	 */
	public function __construct(){}

	/**
	 * @return mixed
	 * @throws Exception
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
	 * Set ext id
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
	 * Get ext id
	 *
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Set meta information for extension
	 *
	 * @param $name
	 * @param string $value
	 */
	public function setMeta($name, $value = '')
	{
		$this->meta[$name] = $value;
	}

	/**
	 * Get meta information for extension
	 *
	 * @param $name
	 * @param string $default_value
	 *
	 * @return mixed|string
	 * @throws RuntimeException
	 */
	public function getMeta($name, $default_value = '')
	{
		$data = $this->meta;

		if($name !== '')
		{
			if(is_array($data) && array_key_exists($name, $data))
			{
				return $data[$name];
			}

			return $default_value;
		}

		throw new RuntimeException('$name is not available');
	}
}