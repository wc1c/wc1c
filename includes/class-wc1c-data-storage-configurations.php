<?php
/**
 * Data storage for Configurations
 *
 * @package Wc1c/Storage
 */
defined('ABSPATH') || exit;

class Wc1c_Data_Storage_Configurations implements Interface_Wc1c_Data_Storage_Configurations
{
	/**
	 * Data stored in meta keys, but not considered "meta" for an object.
	 *
	 * @var array
	 */
	protected $internal_meta_keys = [];

	/**
	 * Meta data which should exist in the DB, even if empty
	 *
	 * @var array
	 */
	protected $must_exist_meta_keys = [];

	/**
	 * @return string
	 */
	public function get_table_name()
	{
		return WC1C_Database()->base_prefix . 'wc1c';
	}

	/**
	 * @return string
	 */
	public function get_meta_table_name()
	{
		return WC1C_Database()->base_prefix . 'wc1c_meta';
	}

	/**
	 * Method to create a new configuration in the database
	 *
	 * @param Wc1c_Configuration $configuration Configuration object
	 *
	 * @throws Wc1c_Exception
	 */
	public function create(&$configuration)
	{
		if(!$configuration->get_date_create('edit'))
		{
			$configuration->set_date_create(time());
		}

		$data =
		[
			'user_id' => get_current_user_id(),
			'name' => $configuration->get_name(),
			'status' => $configuration->get_status(),
			'options' => maybe_serialize($configuration->get_options()),
			'schema' => $configuration->get_schema(),
			'date_create' => gmdate('Y-m-d H:i:s', $configuration->get_date_create('edit')->getTimestamp()),
			'date_modify' => $configuration->get_date_modify(),
			'date_activity' => $configuration->get_date_activity(),
		];

		if(false === WC1C_Database()->insert($this->get_table_name(), $data))
		{
			$configuration_id = new WP_Error('db_insert_error', __('Could not insert configuration into the database'), WC1C_Database()->last_error);
		}
		else
		{
			$configuration_id = (int) WC1C_Database()->insert_id;
		}

		if($configuration_id && !is_wp_error($configuration_id))
		{
			$configuration->set_id($configuration_id);

			$configuration->save_meta_data();
			$configuration->apply_changes();

			// hook
			do_action('wc1c_data_storage_configuration_create', $configuration_id, $configuration);
		}
	}

	/**
	 * Method to read a configuration from the database
	 *
	 * @param Wc1c_Configuration $configuration configuration object
	 *
	 * @throws Wc1c_Exception If invalid configuration
	 */
	public function read(&$configuration)
	{
		//$configuration->set_defaults(); todo: убрать ошибку при сбросе

		if(!$configuration->get_id())
		{
			throw new Wc1c_Exception(__('Invalid configuration', 'wc1c'));
		}

		$table_name = $this->get_table_name();

		$configuration_data = WC1C_Database()->get_row(WC1C_Database()->prepare("SELECT * FROM $table_name WHERE configuration_id = %d LIMIT 1", $configuration->get_id()));

		$configuration->set_props
		(
			array
			(
				'name' => $configuration_data->name,
				'status'=> $configuration_data->status ?: 'draft',
				'options' => maybe_unserialize($configuration_data->options) ?: [],
				'schema' => $configuration_data->schema ?: '',
				'date_create' => 0 < $configuration_data->date_create ? wc1c_string_to_timestamp($configuration_data->date_create) : null,
				'date_modify' => 0 < $configuration_data->date_modify ? wc1c_string_to_timestamp($configuration_data->date_modify) : null,
				'date_activity' => 0 < $configuration_data->date_activity ? wc1c_string_to_timestamp($configuration_data->date_activity) : null,
			)
		);

		$this->read_extra_data($configuration);

		$configuration->set_object_read(true);

		do_action('wc1c_data_storage_configuration_read', $configuration->get_id());
	}

	/**
	 * Method to update a configuration in the database
	 *
	 * @param Wc1c_Configuration $configuration configuration object
	 */
	public function update(&$configuration)
	{
		$configuration->save_meta_data();

		$changes = $configuration->get_changes();

		// Only changed update data changes
		if(array_intersect(
				[
	               'name',
	               'status',
				   'options',
				   'schema',
	               'date_create',
	               'date_modify',
	               'date_activity',
				],
				array_keys($changes)
			)
		)
		{
			$configuration_data =
			[
				'name' => $configuration->get_name(),
				'status' => $configuration->get_status(),
				'options' => maybe_serialize($configuration->get_options()),
				'schema' => $configuration->get_schema(),
				'date_create' => $configuration->get_date_create(),
				'date_modify' => $configuration->get_date_modify(),
				'date_activity' => $configuration->get_date_activity(),
			];

			if($configuration->get_date_create('edit'))
			{
				$configuration_data['date_create'] = gmdate('Y-m-d H:i:s', $configuration->get_date_create('edit')->getTimestamp());
			}

			if(isset($changes['date_modify']) && $configuration->get_date_modify('edit'))
			{
				$configuration_data['date_modify'] = gmdate('Y-m-d H:i:s', $configuration->get_date_modify('edit')->getTimestamp());
			}
			else
			{
				$configuration_data['date_modify'] = current_time('mysql', 1);
			}

			if(isset($changes['date_activity']) && $configuration->get_date_modify('edit'))
			{
				$configuration_data['date_activity'] = gmdate('Y-m-d H:i:s', $configuration->get_date_modify('edit')->getTimestamp());
			}

			WC1C_Database()->update($this->get_table_name(), $configuration_data, ['configuration_id' => $configuration->get_id()]);

			$configuration->read_meta_data(); // Refresh internal meta data, in case things were hooked into `save_post` or another WP hook.
		}

		$configuration->apply_changes();

		do_action('wc1c_data_storage_configuration_update', $configuration->get_id(), $configuration);
	}

	/**
	 * Method to delete a configuration from the database
	 *
	 * @param Wc1c_Configuration $configuration configuration object
	 * @param array $args Array of args to pass to the delete method
	 */
	public function delete(&$configuration, $args = [])
	{
		$configuration_id = $configuration->get_id();

		if(!$configuration_id)
		{
			return;
		}

		$args = wp_parse_args
		(
			$args,
			[
				'force_delete' => false
			]
		);

		if($args['force_delete'])
		{
			do_action('wc1c_data_storage_configuration_before_delete', $configuration_id);

			WC1C_Database()->delete($this->get_table_name(), ['configuration_id' => $configuration->get_id()]);

			$configuration->set_id(0);

			do_action('wc1c_data_storage_configuration_after_delete', $configuration_id);
		}
		else
		{
			do_action('wc1c_data_storage_configuration_before_trash', $configuration_id);

			$configuration->set_status('deleted');
			$configuration->save();

			do_action('wc1c_data_storage_configuration_after_trash', $configuration_id);
		}
	}

	/**
	 * Check if Configuration id is found for any other Configuration IDs
	 *
	 * @param int $configuration_id Configuration ID
	 *
	 * @return bool
	 */
	public function is_existing_by_id($configuration_id)
	{
		return (bool) WC1C_Database()->get_var
		(
			WC1C_Database()->prepare
			(
				"SELECT configuration_id FROM " . $this->get_table_name() . " WHERE  configuration_id = %d LIMIT 1",
				$configuration_id
			)
		);
	}

	/**
	 * Check if Configuration name is found
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function is_existing_by_name($name)
	{
		return (bool) WC1C_Database()->get_var
		(
			WC1C_Database()->prepare(
				"
				SELECT configuration_id
				FROM " . $this->get_table_name() . "
				WHERE
				status != 'deleted'
				AND name = %s
	
				LIMIT 1
				",
				wp_slash($name)
			)
		);
	}

	/**
	 * Read extra data associated with the configuration, like button text or code URL for external configurations.
	 *
	 * @param Wc1c_Configuration $configuration configuration object
	 */
	protected function read_extra_data(&$configuration)
	{
		foreach($configuration->get_extra_data_keys() as $extra_data_key)
		{
			$function = 'set_' . $extra_data_key;
			if(is_callable([$configuration, $function]))
			{
				$configuration->{$function}(
					get_post_meta($configuration->get_id(), '_' . $extra_data_key, true) // todo get_post_meta
				);
			}
		}
	}

	/**
	 * Return list of internal meta keys
	 *
	 * @return array
	 */
	public function get_internal_meta_keys()
	{
		return $this->internal_meta_keys;
	}

	/**
	 * Callback to remove unwanted meta data
	 *
	 * @param object $meta Meta object to check if it should be excluded or not
	 *
	 * @return bool
	 */
	protected function exclude_internal_meta_keys($meta)
	{
		return !in_array($meta->meta_key, $this->internal_meta_keys, true) && 0 !== stripos($meta->meta_key, 'wp_');
	}

	/**
	 * Add new piece of meta
	 *
	 * @param Abstract_Wc1c_Data $object Abstract_Wc1c_Data_Storage object
	 * @param stdClass $meta (containing ->key and ->value)
	 *
	 * @return int meta ID
	 */
	public function add_meta(&$object, $meta)
	{
		$meta_table = $this->get_meta_table_name();

		if(!$meta_table)
		{
			return false;
		}

		if(!$meta->key || !is_numeric($object->get_id()))
		{
			return false;
		}

		$meta_key = wp_unslash($meta->key);
		$meta_value = wp_unslash($meta->value);

		$_meta_value = $meta_value;
		$meta_value  = maybe_serialize($meta_value);

		/**
		 * Fires immediately before meta of a specific type is added.
		 *
		 * @param int $object_id Object ID.
		 * @param string $meta_key Meta key.
		 * @param mixed $meta_value Meta value.
		 */
		do_action("wc1c_data_storage_configuration_meta_add", $object->get_id(), $meta_key, $_meta_value);

		$result = WC1C_Database()->insert
		(
			$meta_table,
			[
				'configuration_id' => $object->get_id(),
				'name' => $meta_key,
				'value' => $meta_value
			]
		);

		if(!$result)
		{
			return false;
		}

		$meta_id = (int) WC1C_Database()->insert_id;

		/**
		 * Fires immediately after meta of a specific type is added
		 *
		 * @param int $meta_id The meta ID after successful update.
		 * @param int $object_id Object ID.
		 * @param string $meta_key Meta key.
		 * @param mixed $meta_value Meta value.
		 */
		do_action("wc1c_data_storage_configuration_meta_added", $meta_id, $object->get_id(), $meta_key, $_meta_value);

		return $meta_id;
	}

	/**
	 * Deletes meta based on meta ID
	 *
	 * @param Abstract_Wc1c_Data $object Abstract_Wc1c_Data_Storage object
	 * @param stdClass $meta (containing at least -> id).
	 *
	 * @return bool
	 */
	public function delete_meta(&$object, $meta)
	{
		$meta_table = $this->get_meta_table_name();

		if(!$meta_table)
		{
			return false;
		}

		if(!$meta->key || !is_numeric($object->get_id()))
		{
			return false;
		}

		$meta_id = (int) $meta->id;
		if($meta_id <= 0)
		{
			return false;
		}

		if(!$this->get_metadata_by_id($meta_id))
		{
			return false;
		}

		// hook
		do_action("wc1c_data_storage_configuration_meta_delete", [$meta_id, $object->get_id(), $meta->key, $meta->value]);

		$result = (bool) WC1C_Database()->delete
		(
			$meta_table,
			['meta_id' => $meta_id]
		);

		// hook
		do_action("wc1c_data_storage_configuration_meta_deleted", [$meta_id, $object->get_id(), $meta->key, $meta->value]);

		return $result;
	}

	/**
	 * Update meta
	 *
	 * @param Abstract_Wc1c_Data $object Abstract_Wc1c_Data_Storage object
	 * @param stdClass $meta (containing ->id, ->key and ->value).
	 *
	 * @return bool
	 */
	public function update_meta(&$object, $meta)
	{
		$meta_table = $this->get_meta_table_name();

		if(!$meta_table)
		{
			return false;
		}

		if(!$meta->key || !is_numeric($object->get_id()))
		{
			return false;
		}

		$meta_id = (int) $meta->id;
		if($meta_id <= 0)
		{
			return false;
		}

		if($_meta = $this->get_metadata_by_id($meta_id))
		{
			$meta_value = maybe_serialize($meta->value);

			$data = array
			(
				'key'   => $meta->key,
				'value' => $meta_value
			);

			$where = array();
			$where['meta_id'] = $meta_id;

			// hook
			do_action("wc1c_data_storage_configuration_meta_update", $meta_id, $object->get_id(), $meta->key, $meta_value);

			$result = WC1C_Database()->update($meta_table, $data, $where, '%s', '%d');

			if(!$result)
			{
				return false;
			}

			// hook
			do_action("wc1c_data_storage_configuration_meta_updated", $meta->meta_id, $object->get_id(), $meta->key, $meta_value);

			return true;
		}

		return false;
	}

	/**
	 * Get meta data by meta ID
	 *
	 * @param int $meta_id ID for a specific meta row
	 *
	 * @return object|false Meta object or false.
	 */
	public function get_metadata_by_id($meta_id)
	{
		$meta_table = $this->get_meta_table_name();

		if(!$meta_table)
		{
			return false;
		}

		$meta_id = (int) $meta_id;
		if($meta_id <= 0)
		{
			return false;
		}

		$meta = WC1C_Database()->get_row(WC1C_Database()->prepare("SELECT * FROM $meta_table WHERE meta_id = %d", $meta_id));

		if(empty($meta))
		{
			return false;
		}

		if(isset($meta->value))
		{
			$meta->value = maybe_unserialize($meta->value);
		}

		return $meta;
	}

	/**
	 * Returns an array of meta for an object.
	 *
	 * @param Abstract_Wc1c_Data $object Abstract_Wc1c_Data object
	 *
	 * @return array
	 */
	public function read_meta(&$object)
	{
		$meta_table = $this->get_meta_table_name();

		$raw_meta_data = WC1C_Database()->get_results
		(
			WC1C_Database()->prepare
			(
				"SELECT meta_id, name, value
				FROM {$meta_table}
				WHERE configuration_id = %d
				ORDER BY meta_id",
				$object->get_id()
			)
		);

		//$this->internal_meta_keys = array_merge(array_map(array($this, 'prefix_key'), $object->get_data_keys()), $this->internal_meta_keys);

		//$meta_data = array_filter($raw_meta_data, array($this, 'exclude_internal_meta_keys'));

		return apply_filters("wc1c_data_storage_configuration_meta_read", $raw_meta_data, $object, $this);
	}

	/**
	 * Internal meta keys we don't want exposed as part of meta_data. This is in
	 * addition to all data props with _ prefix.
	 *
	 * @param string $key Prefix to be added to meta keys
	 *
	 * @return string
	 */
	protected function prefix_key($key)
	{
		return '_' === substr($key, 0, 1) ? $key : '_' . $key;
	}

	/**
	 * Retrieves the total count of table entries
	 *
	 * @return int
	 */
	public function count()
	{
		$count = WC1C_Database()->get_var('SELECT COUNT(*) FROM ' . $this->get_table_name() . ';');

		return (int) $count;
	}

	/**
	 * Retrieves the total count of table entries, filtered by the query parameter
	 *
	 * @param array $query
	 *
	 * @return int
	 */
	public function count_by($query)
	{
		if(!$query || !is_array($query) || count($query) <= 0)
		{
			return false;
		}

		$join = '';
		$where = '';

		if(isset($query['meta_query']))
		{
			$meta_query = new Wc1c_Data_Meta_Query();
			$meta_query->parse_query_vars($query);

			$clauses = $meta_query->get_sql('configuration', $this->get_table_name(), 'configuration_id');

			$join   .= $clauses['join'];
			$where  .= $clauses['where'];

			unset($query['meta_query']);
		}

		$sql_query = 'SELECT COUNT(*) FROM ' . $this->get_table_name() . $join . ' WHERE 1=1 ';
		$sql_query .= $this->parse_query_conditions($query);
		$sql_query .= $where . ';';

		$count = WC1C_Database()->get_var($sql_query);

		return (int) $count;
	}

	/**
	 * Returns an array of data
	 *
	 * @param array $args Args
	 * @param string $type
	 *
	 * @return mixed
	 */
	public function get_data($args = [], $type = OBJECT)
	{
		if(!$args || !is_array($args) || count($args) <= 0)
		{
			return false;
		}

		$join = '';
		$where = '';
		$limit = ' LIMIT 10';
		$offset = '';
		$orderby = '';
		$order = 'asc';

		if(isset($args['orderby']))
		{
			if(!isset($args['order']))
			{
				$args['order'] = $order;
			}

			$orderby = ' ORDER BY ' . $args['orderby'] . ' ' . $args['order'];
			unset($args['orderby'], $args['order']);
		}

		if(isset($args['offset']))
		{
			$offset = ' OFFSET ' . $args['offset'];
			unset($args['offset']);
		}
		if(isset($args['limit']))
		{
			$limit = ' LIMIT ' . $args['limit'];
			unset($args['limit']);
		}

		$fields = WC1C_Database()->base_prefix . 'wc1c.*';

		if(isset($args['fields']) && is_array($args['fields']))
		{
			$raw_field = [];

			foreach($args['fields'] as $field_key => $field)
			{
				if(is_array($field))
				{
					$raw_field[] = WC1C_Database()->base_prefix . $field['name'] . ' as ' . $field['alias'];
					continue;
				}

				$raw_field[] = WC1C_Database()->base_prefix . $field;
			}

			$fields = implode(', ', $raw_field);

			unset($args['fields']);
		}

		if(isset($args['meta_query']))
		{
			$meta_query = new Wc1c_Data_Meta_Query();
			$meta_query->parse_query_vars($args);

			$clauses = $meta_query->get_sql('configuration', $this->get_table_name(), 'configuration_id');

			$join .= $clauses['join'];
			$where .= $clauses['where'];

			unset($args['meta_query']);
		}

		$sql_query = 'SELECT ' . $fields . ' FROM ' . $this->get_table_name() . $join . ' WHERE 1=1 ';

		$sql_query .= $this->parse_query_conditions($args);

		$sql_query .= $where . $orderby . $limit . $offset . ';';

		$data = WC1C_Database()->get_results($sql_query, $type);

		if(!$data)
		{
			return false;
		}

		return $data;
	}

	/**
	 * @param array $query
	 *
	 * @return string
	 */
	private function parse_query_conditions($query)
	{
		$result = '';

		foreach($query as $column_name => $value)
		{
			if(is_array($value))
			{
				if(isset($value['compare_key']) && $value['compare_key'] === 'LIKE')
				{
					$result .= "AND {$column_name} LIKE '%" . esc_sql(WC1C_Database()->esc_like(wp_unslash($value['value']))) . "%' ";
				}
				else
				{
					$valuesIn = implode(', ', array_map('absint', $value));
					$result   .= "AND {$column_name} IN ({$valuesIn}) ";
				}
			}
			elseif(is_string($value))
			{
				$result .= "AND {$column_name} = '{$value}' ";
			}
			elseif(is_numeric($value))
			{
				$value  = absint($value);
				$result .= "AND {$column_name} = {$value} ";
			}
			elseif($value === null)
			{
				$result .= "AND {$column_name} IS NULL ";
			}
		}

		return $result;
	}
}