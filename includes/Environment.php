<?php
/**
 * Namespace
 */
namespace Wc1c;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Traits\SingletonTrait;

/**
 * Environment
 *
 * @package Wc1c
 */
final class Environment
{
	use SingletonTrait;

	/**
	 * @var array Environ data
	 */
	private $data;

	/**
	 * Environment constructor
	 */
	public function __construct(){}

	/**
	 * Get data
	 *
	 * @param $key
	 * @param $default
	 *
	 * @return mixed
	 */
	public function get($key, $default = null)
	{
		if(isset($this->data[$key]))
		{
			return $this->data[$key];
		}
		else
		{
			$key_getter = str_replace(' ', '', ucwords(str_replace('_', ' ', $key)));

			$getter = "init$key_getter";

			if(is_callable([$this, $getter]))
			{
				try
				{
					$getter_value = $this->{$getter}($default);
					$this->set($key, $getter_value);
				}
				catch(Exception $e){}

				return $this->get($key);
			}
		}

		if(false === is_null($default))
		{
			return $default;
		}

		return false;
	}

	/**
	 * Set environ data
	 *
	 * @param $key
	 * @param $value
	 */
	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	/**
	 * Configuration current identifier initializing
	 *
	 * @return bool
	 */
	public function initCurrentConfigurationId()
	{
		$config_id = wc1c()->getVar($_GET['configuration_id'], 0);

		if(0 < $config_id && 99999999 > $config_id)
		{
			if(wc1c()->context()->isInput() || wc1c()->context()->isWc1cAdmin())
			{
				$this->set('current_configuration_id', $config_id);

				return $this->get('current_configuration_id');
			}
		}

		return false;
	}

	/**
	 * WordPress upload directory
	 *
	 * @return bool
	 * @throws RuntimeException
	 */
	public function initUploadDirectory()
	{
		if(false === function_exists('wp_upload_dir'))
		{
			throw new RuntimeException('function wp_upload_dir is not exists');
		}

		$wp_upload_dir = wp_upload_dir();

		$this->set('upload_directory', $wp_upload_dir['basedir']);

		return $this->get('upload_directory');
	}

	/**
	 * PHP post max size
	 */
	public function initPhpPostMaxSize()
	{
		$this->set('php_post_max_size', ini_get('post_max_size'));

		return $this->get('php_post_max_size');
	}

	/**
	 * PHP max execution time
	 */
	public function initPhpMaxExecutionTime()
	{
		$this->set('php_max_execution_time', ini_get('max_execution_time'));

		return $this->get('php_max_execution_time');
	}

	/**
	 * Wc1c upload directory
	 *
	 * @return bool
	 */
	public function initWc1cUploadDirectory()
	{
		$wc1c_upload_dir = $this->get('upload_directory') . DIRECTORY_SEPARATOR . 'wc1c';

		$this->set('wc1c_upload_directory', $wc1c_upload_dir);

		return $this->get('wc1c_upload_directory');
	}

	/**
	 * Wc1c version
	 *
	 * @return bool
	 */
	public function initWc1cVersion()
	{
		if(!function_exists('get_file_data'))
		{
			throw new RuntimeException('Function get_file_data is not exists');
		}

		$plugin_data = get_file_data(WC1C_PLUGIN_FILE, ['Version' => 'Version']);

		$this->set('wc1c_version', $plugin_data['Version']);

		return $this->get('wc1c_version');
	}

	/**
	 * Get all data
	 *
	 * @return array
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Set all data
	 *
	 * @param array $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}
}