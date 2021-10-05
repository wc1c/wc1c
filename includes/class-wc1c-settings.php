<?php
/**
 * Settings class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Settings implements Interface_Wc1c_Settings
{
	/**
	 * Name option in wp_options
	 *
	 * @var string
	 */
	private $option_name = 'wc1c';

	/**
	 * Current data
	 *
	 * @var array
	 */
	private $data = [];

	/**
	 * Wc1c_Settings constructor
	 */
	public function __construct(){}

	/**
	 * Initializing
	 *
	 * @return void
	 * @throws Exception
	 */
	public function init()
	{
		// get data from wp_options
		$settings = get_site_option($this->option_name, []);

		// hook
		$settings = apply_filters('wc1c_settings_data_init', $settings);

		if(!is_array($settings))
		{
			throw new Wc1c_Exception_Runtime('init: $settings is not array');
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
			throw new Wc1c_Exception_Runtime('init: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Set setting data - single or all
	 *
	 * @param string|array $setting_data
	 * @param string $setting_key
	 *
	 * @return boolean
	 * @throws Exception|Wc1c_Exception_Runtime
	 */
	public function set($setting_data = '', $setting_key = '')
	{
		if(empty($setting_key) && !is_array($setting_data))
		{
			return false;
		}

		$current_data = $this->get_data();

		if(is_array($setting_data) && empty($setting_key))
		{
			$current_data = array_merge
			(
				$current_data,
				$setting_data
			);
		}
		else
		{
			$current_data[$setting_key] = $setting_data;
		}

		try
		{
			$this->set_data($current_data);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('set: exception - ' . $e->getMessage());
		}

		return true;
	}

	/**
	 * Save
	 *
	 * @param $settings_data null|array - optional
	 *
	 * @return bool
	 * @throws Exception|Wc1c_Exception_Runtime
	 */
	public function save($settings_data = null)
	{
		$current_data = $this->get_data();

		if(is_array($settings_data))
		{
			$settings_data = array_merge($current_data, $settings_data);
		}
		else
		{
			$settings_data = $current_data;
		}

		$settings_data = apply_filters('wc1c_settings_data_save', $settings_data);

		try
		{
			$this->set_data($settings_data);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('save: exception - ' . $e->getMessage());
		}

		/**
		 * Update in DB
		 *
		 * Required WP 4.2.0 - autoload option
		 */
		return update_option($this->option_name, $settings_data, 'no');
	}

	/**
	 * Get settings - all or single
	 *
	 * @param string $setting_key - optional
	 * @param string $default_return - default data, optional
	 *
	 * @return mixed
	 * @throws Wc1c_Exception_Runtime
	 */
	public function get($setting_key = '', $default_return = '')
	{
		try
		{
			$data = $this->get_data();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('get: exception - ' . $e->getMessage());
		}

		if('' !== $setting_key)
		{
			if(array_key_exists($setting_key, $data))
			{
				return $data[$setting_key];
			}

			return $default_return;
		}

		return $data;
	}

	/**
	 * Get all data
	 *
	 * @return array
	 * @throws Exception
	 */
	private function get_data()
	{
		if(!is_array($this->data))
		{
			throw new Wc1c_Exception_Runtime('get_data: $data is not valid array');
		}

		return $this->data;
	}

	/**
	 * Set all data
	 *
	 * @param $data
	 *
	 * @return void
	 * @throws Exception
	 */
	private function set_data($data = [])
	{
		if(!is_array($data))
		{
			throw new Wc1c_Exception_Runtime('set_data: $data is not valid');
		}

		$this->data = $data;
	}
}