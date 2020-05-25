<?php
/**
 * Configuration class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Configuration
{
	/**
	 * Raw data
	 *
	 * @var array
	 */
	private $data;

	/**
	 * Available configuration statuses
	 */
	private $available_statuses =
	[
		'draft', 'active', 'inactive', 'error', 'processing'
	];

	/**
	 * Wc1c_Configuration constructor
	 *
	 * @param array
	 */
	public function __construct($data = [])
	{
		if(!empty($data))
		{
			$this->set_data($data);
		}
	}

	/**
	 * Load raw data
	 *
	 * @param $data
	 *
	 * @return $this
	 */
	public function set_data($data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Get configuration id
	 *
	 * @return mixed
	 */
	public function get_id()
	{
		if(isset($this->data['config_id']))
		{
			return apply_filters('wc1c_configuration_get_id', $this->data['config_id']);
		}

		return false;
	}

	/**
	 * Set configuration id
	 *
	 * @param $name
	 *
	 * @return $this
	 */
	public function set_id($name)
	{
		$this->data['config_id'] = $name;

		return $this;
	}

	/**
	 * @param bool $id
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function load($id = false)
	{
		if(false === $id)
		{
			$id = $this->get_id();
		}

		$config_query = 'SELECT * from ' . WC1C_Db()->base_prefix . 'wc1c WHERE config_id = ' . $id;
		$config_results = WC1C_Db()->get_results($config_query, ARRAY_A);

		if(empty($config_results))
		{
			throw new Exception('load: configuration not found');
		}

		$this->set_data($config_results[0]);

		return true;
	}

	/**
	 * Get configuration name
	 *
	 * @return mixed
	 */
	public function get_name()
	{
		if(isset($this->data['config_name']))
		{
			return apply_filters('wc1c_configuration_get_name', $this->data['config_name']);
		}

		return false;
	}

	/**
	 * Get configuration statuses
	 *
	 * @return array
	 */
	public function get_available_statuses()
	{
		return $this->available_statuses;
	}

	/**
	 * Set configuration statuses
	 *
	 * @param array $available_statuses
	 *
	 * @return $this
	 */
	public function set_available_statuses($available_statuses)
	{
		$this->available_statuses = $available_statuses;

		return $this;
	}

	/**
	 * Set configuration name
	 *
	 * @param $name
	 *
	 * @return $this
	 */
	public function set_name($name)
	{
		$this->data['config_name'] = $name;

		return $this;
	}

	/**
	 * Set configuration status
	 *
	 * @param $status
	 *
	 * @return $this
	 */
	public function set_status($status)
	{
		$this->data['status'] = $status;

		return $this;
	}

	/**
	 * Get configuration status
	 *
	 * @return mixed
	 */
	public function get_status()
	{
		if(isset($this->data['status']))
		{
			return $this->data['status'];
		}

		return false;
	}

	/**
	 * Get configuration schema
	 *
	 * @return mixed
	 */
	public function get_schema()
	{
		if(isset($this->data['schema']))
		{
			return $this->data['schema'];
		}

		return false;
	}

	/**
	 * Get configuration options
	 *
	 * @return mixed
	 */
	public function get_options()
	{
		return maybe_unserialize($this->data['options']);
	}

	/**
	 * Set configuration options
	 *
	 * @param $options
	 *
	 * @return $this
	 */
	public function set_options($options)
	{
		$this->data['options'] = maybe_serialize($options);

		return $this;
	}

	/**
	 * Get configuration create date
	 *
	 * @return mixed
	 */
	public function get_date_create()
	{
		if(isset($this->data['date_create']))
		{
			return $this->data['date_create'];
		}

		return false;
	}

	/**
	 * Get configuration modify date
	 *
	 * @return mixed
	 */
	public function get_date_modify()
	{
		if(isset($this->data['date_modify']))
		{
			return $this->data['date_modify'];
		}

		return false;
	}

	/**
	 * Get configuration activity date
	 *
	 * @return mixed
	 */
	public function get_date_activity()
	{
		if(isset($this->data['date_activity']))
		{
			return $this->data['date_activity'];
		}

		return false;
	}

	/**
	 * Set configuration activity date
	 *
	 * @param mixed $date_activity
	 *
	 * @return $this
	 */
	public function set_date_activity($date_activity = null)
	{
		if(is_null($date_activity))
		{
			$date_activity = current_time('mysql');
		}

		$this->data['date_activity'] = $date_activity;

		return $this;
	}

	/**
	 * Set configuration modify date
	 *
	 * @param mixed $date_modify
	 *
	 * @return $this
	 */
	public function set_date_modify($date_modify = null)
	{
		if(is_null($date_modify))
		{
			$date_modify = current_time('mysql');
		}

		$this->data['date_modify'] = $date_modify;

		return $this;
	}

	/**
	 * Set configuration create date
	 *
	 * @param mixed $date_create
	 *
	 * @return $this
	 */
	public function set_date_create($date_create = null)
	{
		if(is_null($date_create))
		{
			$date_create = current_time('mysql');
		}

		$this->data['date_create'] = $date_create;

		return $this;
	}

	/**
	 * Get configuration raw data
	 *
	 * @return mixed
	 */
	public function get_data()
	{
		return $this->data;
	}

	/**
	 * Check configuration
	 *
	 * @return bool
	 */
	public function check()
	{
		/**
		 * Name empty
		 */
		if(false === $this->get_name() || $this->get_name() === '')
		{
			return false;
		}

		return true;
	}

	/**
	 * Update or create configuration data to db
	 *
	 * @return false|integer
	 */
	public function save()
	{
		if(true !== $this->check())
		{
			return false;
		}

		if(false === $this->get_id())
		{
			$this->set_date_create(current_time('mysql'));
			$this->set_date_modify(current_time('mysql'));
			$this->set_date_activity(current_time('mysql'));

			$insert_result = WC1C_Db()->insert(WC1C_Db()->base_prefix . 'wc1c', $this->get_data());

			if($insert_result !== false)
			{
				return true;
			}

			return false;
		}

		$update_result = WC1C_Db()->update(WC1C_Db()->base_prefix . 'wc1c', $this->get_data(),
		    [
				'config_id' => $this->get_id(),
				//'site_id' => '0'
			]
		);

		if($update_result !== false || $update_result !== 0)
		{
			return true;
		}

		return false;
	}
}