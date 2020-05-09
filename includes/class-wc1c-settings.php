<?php
/**
 * Settings class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Settings
{
	/**
	 * @var array
	 */
	private $data = [];

	/**
	 * Wc1c_Settings constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Settings loading
	 *
	 * @throws Exception
	 */
	public function load()
	{
		$settings = get_site_option('wc1c', []);

		/**
		 * Loading with external code
		 */
		$settings = apply_filters('wc1c_settings_loading', $settings);

		if(!is_array($settings))
		{
			throw new Exception('load: $settings is not array');
		}

		$settings = array_merge
		(
			$this->get_data(),
			$settings
		);

		try
		{
			$this->set_data($settings);
		}
		catch(Exception $e)
		{
			throw new Exception('set_data: $settings is not array');
		}
	}

	/**
	 * Save plugin settings
	 *
	 * @param $settings array
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function save($settings)
	{
		$settings = apply_filters('wc1c_settings_save', $settings);

		try
		{
			$this->set_data($settings);
		}
		catch(Exception $e)
		{
			throw new Exception('set_data: $settings is not array');
		}

		/**
		 * Update in DB
		 *
		 * Required WP 4.2.0 autoload option
		 */
		return update_option('wc1c', $settings, 'no');
	}

	/**
	 * Get settings
	 *
	 * @param string $key - optional
	 * @param null $default
	 *
	 * @return array|bool|mixed
	 * @throws Exception
	 */
	public function get($key = '', $default = null)
	{
		$data = $this->get_data();

		if(empty($data))
		{
			try
			{
				$this->load();
				$data = $this->get_data();
			}
			catch(Exception $e)
			{
				throw new Exception('get: load exception - ' . $e->getMessage());
			}
		}

		if($key !== '')
		{
			if(is_array($data) && array_key_exists($key, $data))
			{
				return $data[$key];
			}

			return $default;
		}

		return $data;
	}

	/**
	 * Get all data
	 *
	 * @return mixed
	 */
	public function get_data()
	{
		return $this->data;
	}

	/**
	 * Set settings
	 *
	 * @param $data
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function set_data($data)
	{
		if(is_array($data))
		{
			$this->data = $data;

			return true;
		}

		throw new Exception('set_data: $data is not valid');
	}
}