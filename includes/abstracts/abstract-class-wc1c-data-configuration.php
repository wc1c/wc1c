<?php
/**
 * Abstract data
 *
 * Implemented by classes using the same CRUD(s) pattern
 *
 * @package Wc1c/Abstracts
 */
defined('ABSPATH') || exit;

abstract class Abstract_Wc1c_Data_Configuration extends Abstract_Wc1c_Data
{
	/**
	 * This is the name of this object type
	 *
	 * @var string
	 */
	protected $object_type = 'configuration';

	/**
	 * @var array|null
	 */
	protected $meta_data = null;

	/**
	 * Filter null meta values from array
	 *
	 * @param mixed $meta Meta value to check
	 *
	 * @return bool
	 */
	protected function filter_null_meta($meta)
	{
		return !is_null($meta->value);
	}

	/**
	 * Get all meta data for object
	 *
	 * @return array of objects
	 */
	public function get_meta_data()
	{
		$this->maybe_read_meta_data();

		return array_values(array_filter($this->meta_data, [$this, 'filter_null_meta']));
	}

	/**
	 * Returns all data for this object
	 *
	 * @return array
	 */
	public function get_data()
	{
		return array_merge(['id' => $this->get_id()], $this->data, ['meta_data' => $this->get_meta_data()]);
	}

	/**
	 * Set a collection of props in one go, collect any errors, and return the result
	 *
	 * Only sets using public methods
	 *
	 * @param array $props Key value pairs to set. Key is the prop and should map to a setter function name
	 * @param string $context In what context to run this
	 *
	 * @return bool|WP_Error
	 */
	public function set_props($props, $context = 'set')
	{
		$errors = false;

		foreach($props as $prop => $value)
		{
			try
			{
				if(is_null($value) || in_array($prop, ['prop', 'date_prop', 'meta_data'], true))
				{
					continue;
				}

				$setter = "set_$prop";

				if(is_callable([$this, $setter]))
				{
					$this->{$setter}($value);
				}
			}
			catch(Exception $e)
			{
				if(!$errors)
				{
					$errors = new WP_Error();
				}

				$errors->add($e->getCode(), $e->getMessage());
			}
		}

		return $errors && count($errors->get_error_codes()) ? $errors : true;
	}

	/**
	 * Check if the key is an internal one
	 *
	 * @param string $key key to check
	 *
	 * @return bool true if it's an internal key, false otherwise
	 */
	protected function is_internal_meta_key($key)
	{
		$internal_meta_key = !empty($key) && $this->storage && in_array($key, $this->storage->get_internal_meta_keys(), true);

		if(!$internal_meta_key)
		{
			return false;
		}

		$has_setter_or_getter = is_callable([$this, 'set_' . $key]) || is_callable([$this, 'get_' . $key]);

		if(!$has_setter_or_getter)
		{
			return false;
		}

		return true;
	}

	/**
	 * Get Meta Data by Key
	 *
	 * @param string $key Meta Key
	 * @param bool $single return first found meta with key, or all with $key
	 * @param string $context What the value is for. Valid values are view and edit
	 *
	 * @return mixed
	 */
	public function get_meta($key = '', $single = true, $context = 'view')
	{
		if($this->is_internal_meta_key($key))
		{
			$function = 'get_' . $key;

			if(is_callable([$this, $function]))
			{
				return $this->{$function}();
			}
		}

		$this->maybe_read_meta_data();

		$meta_data = $this->get_meta_data();
		$array_keys = array_keys(wp_list_pluck($meta_data, 'key'), $key, true);
		$value = $single ? '' : [];

		if(!empty($array_keys))
		{
			// We don't use the $this->meta_data property directly here because we don't want meta with a null value (i.e. meta which has been deleted via $this->delete_meta_data()).
			if($single)
			{
				$value = $meta_data[current($array_keys)]->value;
			}
			else
			{
				$value = array_intersect_key($meta_data, array_flip($array_keys));
			}
		}

		if('view' === $context)
		{
			$value = apply_filters($this->get_hook_prefix() . $key, $value, $this);
		}

		return $value;
	}

	/**
	 * See if meta data exists, since get_meta always returns a '' or array()
	 *
	 * @param string $key meta Key
	 *
	 * @return boolean
	 */
	public function meta_exists($key = '')
	{
		$this->maybe_read_meta_data();

		$array_keys = wp_list_pluck($this->get_meta_data(), 'key');

		return in_array($key, $array_keys, true);
	}

	/**
	 * Set all meta data from array
	 *
	 * @param array $data Key/Value pairs
	 */
	public function set_meta_data($data)
	{
		if(!empty($data) && is_array($data))
		{
			$this->maybe_read_meta_data();

			foreach($data as $meta)
			{
				$meta = (array) $meta;
				if(isset($meta['key'], $meta['value'], $meta['id']))
				{
					$this->meta_data[] = new Wc1c_Data_Meta
					(
						[
							'id' => $meta['id'],
							'key' => $meta['key'],
							'value' => $meta['value'],
						]
					);
				}
			}
		}
	}

	/**
	 * Add meta data
	 *
	 * @param string $key Meta key
	 * @param string|array $value Meta value
	 * @param bool $unique Should this be a unique key?
	 *
	 * @return void
	 */
	public function add_meta_data($key, $value, $unique = false)
	{
		if($this->is_internal_meta_key($key))
		{
			$function = 'set_' . $key;

			if(is_callable([$this, $function]))
			{
				return $this->{$function}($value);
			}
		}

		$this->maybe_read_meta_data();
		if($unique)
		{
			$this->delete_meta_data($key);
		}

		$this->meta_data[] = new Wc1c_Data_Meta
		(
			[
				'key' => $key,
				'value' => $value,
			]
		);
	}

	/**
	 * Update meta data by key or ID, if provided
	 *
	 * @param string $key Meta key
	 * @param string|array $value Meta value
	 * @param int $meta_id Meta ID
	 *
	 * @return mixed|void
	 */
	public function update_meta_data($key, $value, $meta_id = 0)
	{
		if($this->is_internal_meta_key($key))
		{
			$function = 'set_' . $key;

			if(is_callable([$this, $function]))
			{
				return $this->{$function}($value);
			}
		}

		$this->maybe_read_meta_data();

		$array_key = false;

		if($meta_id)
		{
			$array_keys = array_keys(wp_list_pluck($this->meta_data, 'id'), $meta_id, true);
			$array_key  = $array_keys ? current($array_keys) : false;
		}
		else
		{
			$matches = [];
			foreach($this->meta_data as $meta_data_array_key => $meta)
			{
				if($meta->key === $key)
				{
					$matches[] = $meta_data_array_key;
				}
			}

			if(!empty($matches))
			{
				foreach($matches as $meta_data_array_key)
				{
					$this->meta_data[$meta_data_array_key]->value = null;
				}
				$array_key = current($matches);
			}
		}

		if(false !== $array_key)
		{
			$meta = $this->meta_data[$array_key];
			$meta->key = $key;
			$meta->value = $value;
		}
		else
		{
			$this->add_meta_data($key, $value, true);
		}
	}

	/**
	 * Delete meta data
	 *
	 * @param string $key Meta key
	 */
	public function delete_meta_data($key)
	{
		$this->maybe_read_meta_data();

		$array_keys = array_keys(wp_list_pluck($this->meta_data, 'key'), $key, true);

		if($array_keys)
		{
			foreach($array_keys as $array_key)
			{
				$this->meta_data[$array_key]->value = null;
			}
		}
	}

	/**
	 * Delete meta data
	 *
	 * @param int $mid Meta ID
	 */
	public function delete_meta_data_by_id($mid)
	{
		$this->maybe_read_meta_data();
		$array_keys = array_keys(wp_list_pluck($this->meta_data, 'id'), (int) $mid, true);

		if($array_keys)
		{
			foreach($array_keys as $array_key)
			{
				$this->meta_data[$array_key]->value = null;
			}
		}
	}

	/**
	 * Read meta data if null
	 */
	protected function maybe_read_meta_data()
	{
		if(is_null($this->meta_data))
		{
			$this->read_meta_data();
		}
	}

	/**
	 * Read Meta Data from the database. Ignore any internal properties
	 *
	 * Uses it's own caches because get_metadata does not provide meta_ids
	 */
	public function read_meta_data()
	{
		$this->meta_data = [];

		if(!$this->get_id())
		{
			return;
		}

		if(!$this->storage)
		{
			return;
		}

		$raw_meta_data = $this->storage->read_meta($this);

		if($raw_meta_data)
		{
			foreach($raw_meta_data as $meta)
			{
				$this->meta_data[] = new Wc1c_Data_Meta
				(
					[
						'id' => (int) $meta->meta_id,
						'key' => $meta->name,
						'value' => maybe_unserialize($meta->value),
					]
				);
			}
		}
	}

	/**
	 * Update Meta Data in the database
	 */
	public function save_meta_data()
	{
		if(!$this->storage || is_null($this->meta_data))
		{
			return;
		}

		foreach($this->meta_data as $array_key => $meta)
		{
			if(is_null($meta->value))
			{
				if(!empty($meta->id))
				{
					$this->storage->delete_meta($this, $meta);
					unset($this->meta_data[$array_key]);
				}
			}
			elseif(empty($meta->id))
			{
				$meta->id = $this->storage->add_meta($this, $meta);
				$meta->apply_changes();
			}
			else if($meta->get_changes())
			{
				$this->storage->update_meta($this, $meta);
				$meta->apply_changes();
			}
		}
	}
}