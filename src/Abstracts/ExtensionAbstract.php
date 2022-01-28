<?php namespace Wc1c\Abstracts;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;

/**
 * ExtensionAbstract
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

	/**
	 * Load meta data by plugin file
	 *
	 * @param $file
	 * @param string $locale
	 *
	 * @return bool
	 */
	public function loadMetaByPlugin($file, $locale = '')
	{
		$default_headers =
		[
			'Name' => 'Plugin Name',
			'PluginURI' => 'Plugin URI',
			'Version' => 'Version',
			'Description' => 'Description',
			'Author' => 'Author',
			'AuthorURI' => 'Author URI',
			'TextDomain' => 'Text Domain',
			'DomainPath' => 'Domain Path',
			'Network' => 'Network',
			'RequiresWP' => 'Requires at least',
			'RequiresPHP' => 'Requires PHP',
			'RequiresWC' => 'WC requires at least',
			'TestedWC' => 'WC tested up to',
			'RequiresWC1C' => 'Requires WC1C',
			'TestedWC1C' => 'WC1C tested up to',
		];

		$plugin_data = get_file_data($file, $default_headers, 'plugin');

		if(!isset($plugin_data['Version']))
		{
			return false;
		}

		if('' === $locale)
		{
			$locale = $plugin_data['TextDomain'];
		}

		$this->setMeta('version', $plugin_data['Version']);
		$this->setMeta('version_php_min', $plugin_data['RequiresPHP']);
		$this->setMeta('version_wp_min', $plugin_data['RequiresWP']);
		$this->setMeta('version_wc_min', $plugin_data['RequiresWC']);
		$this->setMeta('version_wc_max', $plugin_data['TestedWC']);
		$this->setMeta('version_wc1c_min', $plugin_data['RequiresWC1C']);
		$this->setMeta('version_wc1c_max', $plugin_data['TestedWC1C']);
		$this->setMeta('author', __($plugin_data['Author'], $locale));
		$this->setMeta('name', __($plugin_data['Name'], $locale));
		$this->setMeta('description', __($plugin_data['Description'], $locale));

		return true;
	}
}