<?php
/**
 * Environment class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Environment
{
	/**
	 * Environ data
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Wc1c_Environment constructor
	 */
	public function __construct()
	{
		$this->init_wc1c_version();
		$this->init_current_configuration_id();
		$this->init_upload_directory();
		$this->init_wc1c_upload_directory();
		$this->init_php_post_max_size();
		$this->init_php_max_execution_time();
	}

	/**
	 * Get environ data
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
	public function init_current_configuration_id()
	{
		$config_id = wc1c_get_var($_GET['config_id'], 0);

		if(0 < $config_id && 99999999 > $config_id)
		{
			if(is_wc1c_api_request() || is_wc1c_admin_request())
			{
				$this->set('current_configuration_id', $config_id);

				return true;
			}
		}

		return false;
	}

	/**
	 * WordPress upload directory
	 *
	 * @return bool
	 * @throws Wc1c_Exception_Runtime
	 */
	public function init_upload_directory()
	{
		if(false === function_exists('wp_upload_dir'))
		{
			throw new Wc1c_Exception_Runtime('function wp_upload_dir is not exists');
		}

		$wp_upload_dir = wp_upload_dir();

		$this->set('upload_directory', $wp_upload_dir['basedir']);

		return true;
	}

	/**
	 * PHP post max size
	 */
	public function init_php_post_max_size()
	{
		$this->set('php_post_max_size', ini_get('post_max_size'));

		return true;
	}

	/**
	 * PHP max execution time
	 */
	public function init_php_max_execution_time()
	{
		$this->set('php_max_execution_time', ini_get('max_execution_time'));

		return true;
	}

	/**
	 * Wc1c upload directory
	 *
	 * @return bool
	 */
	public function init_wc1c_upload_directory()
	{
		$wc1c_upload_dir = $this->get('upload_directory') . DIRECTORY_SEPARATOR . 'wc1c';

		$this->set('wc1c_upload_directory', $wc1c_upload_dir);

		return true;
	}

	/**
	 * Wc1c version
	 *
	 * @return bool
	 */
	public function init_wc1c_version()
	{
		if(!defined('WC1C_PLUGIN_VERSION'))
		{
			throw new Wc1c_Exception_Runtime('Constant WC1C_PLUGIN_VERSION is not defined');
		}

		$this->set('wc1c_version', WC1C_PLUGIN_VERSION);
		return true;
	}

	/**
	 * Get all data
	 *
	 * @return array
	 */
	public function get_data()
	{
		return $this->data;
	}

	/**
	 * Set all data
	 *
	 * @param array $data
	 */
	public function set_data($data)
	{
		$this->data = $data;
	}
}