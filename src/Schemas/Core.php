<?php namespace Wc1c\Schemas;

defined('ABSPATH') || exit;

use Wc1c\Configuration;
use Wc1c\Data\Storage;
use Wc1c\Data\Storages\ConfigurationsStorage;
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Traits\SingletonTrait;

/**
 * Core
 *
 * @package Wc1c\Schemas
 */
final class Core
{
	use SingletonTrait;

	/**
	 * @var array All loaded
	 */
	private $schemas = [];

	/**
	 * Set
	 *
	 * @param array $schemas
	 *
	 * @return void
	 * @throws Exception
	 */
	public function set($schemas)
	{
		if(!is_array($schemas))
		{
			throw new Exception('$schemas is not valid');
		}

		$this->schemas = $schemas;
	}

	/**
	 * Initializing schemas
	 *
	 * @param integer|Configuration $configuration
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function init($configuration)
	{
		if(false === $configuration)
		{
			throw new Exception('$configuration is false');
		}

		if(!is_object($configuration))
		{
			try
			{
				/** @var ConfigurationsStorage $storage_configurations */
				$storage_configurations = Storage::load('configuration');
			}
			catch(Exception $e)
			{
				throw $e;
			}

			if(!$storage_configurations->isExistingById($configuration))
			{
				throw new Exception('$configuration is not exists');
			}

			try
			{
				$configuration = new Configuration($configuration);
			}
			catch(Exception $e)
			{
				throw $e;
			}
		}

		if(!$configuration instanceof Configuration)
		{
			throw new Exception('$configuration is not instanceof Configuration');
		}

		try
		{
			$schemas = $this->get();
		}
		catch(Exception $e)
		{
			throw $e;
		}

		if(!is_array($schemas))
		{
			throw new Exception('$schemas is not array');
		}

		$schema_id = $configuration->getSchema();

		if(!array_key_exists($schema_id, $schemas))
		{
			throw new Exception('schema not found by id: ' . $schema_id);
		}

		if(!is_object($schemas[$schema_id]))
		{
			throw new Exception('$schemas[$schema_id] is not object');
		}

		$init_schema = $schemas[$schema_id];

		if($init_schema->isInitialized())
		{
			throw new Exception('old initialized, $schema_id: ' . $schema_id);
		}

		if(!method_exists($init_schema, 'init'))
		{
			throw new Exception('method init not found, $schema_id: ' . $schema_id);
		}

		$current_configuration_id = $configuration->getId();

		$init_schema->setPrefix('wc1c_prefix_' . $schema_id . '_' . $current_configuration_id);
		$init_schema->setConfiguration($configuration);
		$init_schema->setConfigurationPrefix('wc1c_configuration_' . $current_configuration_id);

		try
		{
			$init_schema_result = $init_schema->init();
		}
		catch(Exception $e)
		{
			throw new Exception('Exception by schema - ' . $e->getMessage());
		}

		if(true !== $init_schema_result)
		{
			throw new Exception('Schema is not initialized');
		}

		$init_schema->setInitialized(true);

		return $init_schema;
	}

	/**
	 * Get schemas
	 *
	 * @param string $schema_id
	 *
	 * @return array|mixed
	 * @throws RuntimeException
	 */
	public function get($schema_id = '')
	{
		$schema_id = strtolower($schema_id);

		if('' !== $schema_id)
		{
			if(array_key_exists($schema_id, $this->schemas))
			{
				return $this->schemas[$schema_id];
			}

			throw new RuntimeException('Schema by ID is unavailable');
		}

		return $this->schemas;
	}

	/**
	 * Schemas loading
	 *
	 * @throws RuntimeException
	 */
	public function load()
	{
		$schemas = [];

		try
		{
			$schema_productscml = new Productscml\Core();
		}
		catch(Exception $e)
		{
			throw new RuntimeException('Schema init exception - ' . $e->getMessage());
		}

		$schemas[strtolower($schema_productscml->getId())] = $schema_productscml;

		if('yes' === wc1c()->settings()->get('extensions_schemas', 'yes'))
		{
			$schemas = apply_filters('wc1c_schemas_loading', $schemas);
		}

		wc1c()->log()->debug(__('Schemas loaded.', 'wc1c'), ['schemas' => $schemas]);

		try
		{
			$this->set($schemas);
		}
		catch(Exception $e)
		{
			throw new RuntimeException('Set exception - ' . $e->getMessage());
		}
	}
}