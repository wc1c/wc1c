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
use wpdb;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Wc1c\Data\Storage;
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Interfaces\SettingsInterface;
use Wc1c\Log\CoreLog;
use Wc1c\Traits\SingletonTrait;
use Wc1c\Settings\MainSettings;

/**
 * Core
 *
 * @package Wc1c
 */
final class Core
{
	use SingletonTrait;
	use LoggerAwareTrait;

	/**
	 * @var Environment
	 */
	private $environment;

	/**
	 * @var Timer
	 */
	private $timer;

	/**
	 * @var MainSettings
	 */
	private $settings;

	/**
	 * @var Input
	 */
	private $input;

	/**
	 * @var array Loaded configurations
	 */
	private $configurations = [];

	/**
	 * @var array Loaded extensions
	 */
	private $extensions = [];

	/**
	 * @var array Loaded schemas
	 */
	private $schemas = [];

	/**
	 * @var array All loaded tools
	 */
	private $tools = [];

	/**
	 * Core constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// hook
		do_action(WC1C_PREFIX . 'before_loading');

		// init
		add_action('init', [$this, 'init'], 3);

		// admin
		if(false !== is_admin())
		{
			add_action('init', [Admin::class, 'instance'], 5);
		}

		// hook
		do_action(WC1C_PREFIX . 'after_loading');
	}

	/**
	 * Initialization
	 *
	 * @return bool
	 */
	public function init()
	{
		// hook
		do_action(WC1C_PREFIX . 'before_init');

		try
		{
			$this->localization();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->loadTimer();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->loadExtensions();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->initExtensions();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->loadSchemas();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		if(false !== is_wc1c_api_request() || false !== is_wc1c_admin_request())
		{
			try
			{
				//$this->loadTools();
			}
			catch(Exception $e)
			{
				wc1c()->log()->alert('init: exception - ' . $e->getMessage());
				return false;
			}
		}

		if(false !== is_wc1c_api_request())
		{
			try
			{
				$this->loadingInput();
			}
			catch(Exception $e)
			{
				wc1c()->log()->alert('init: exception - ' . $e->getMessage());
				return false;
			}
		}

		// hook
		do_action(WC1C_PREFIX . 'after_init');

		return true;
	}

	/**
	 * Environment
	 *
	 * @return Environment
	 */
	public function environment()
	{
		if(is_null($this->environment))
		{
			$this->environment = Environment::instance();
		}

		return $this->environment;
	}

	/**
	 * Log
	 *
	 * @return LoggerInterface
	 */
	public function log()
	{
		if(is_null($this->log))
		{
			$logger = new CoreLog();

			$this->setLog($logger);
		}

		return $this->log;
	}

	/**
	 * Get settings
	 *
	 * @param string $context
	 *
	 * @return SettingsInterface
	 * @throws RuntimeException
	 */
	public function settings($context = 'main')
	{
		if(!$this->settings instanceof SettingsInterface)
		{
			try
			{
				$settings = new MainSettings();
				$settings->init();
			}
			catch(Exception $e)
			{
				throw new RuntimeException('exception - ' . $e->getMessage());
			}

			$this->settings = $settings;
		}

		return $this->settings;
	}

	/**
	 * Initializing extensions
	 *
	 * @param string $extension_id If an extension ID is specified, only the specified extension is loaded
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function initExtensions($extension_id = '')
	{
		try
		{
			$extensions = $this->getExtensions();
		}
		catch(Exception $e)
		{
			throw new Exception('$extensions - ' . $e->getMessage());
		}

		if(!is_array($extensions))
		{
			throw new Exception('$extensions is not array');
		}

		/**
		 * Init specified extension
		 */
		if('' !== $extension_id)
		{
			if(!array_key_exists($extension_id, $extensions))
			{
				throw new Exception('extension not found by id');
			}

			$init_extension = $extensions[$extension_id];

			if(!is_object($init_extension))
			{
				throw new Exception('$extensions[$extension_id] is not object');
			}

			if($init_extension->is_initialized())
			{
				throw new Exception('old initialized');
			}

			if(!method_exists($init_extension, 'init'))
			{
				throw new Exception('method init not found');
			}

			try
			{
				$init_extension->init();
			}
			catch(Exception $e)
			{
				throw new Exception('exception by extension - ' . $e->getMessage());
			}

			$init_extension->setInitialized(true);

			return true;
		}

		/**
		 * Init all extensions
		 */
		foreach($extensions as $extension => $extension_object)
		{
			try
			{
				$this->initExtensions($extension);
			}
			catch(Exception $e)
			{
				wc1c()->log()->error($e->getMessage(), $e);
				continue;
			}
		}

		return true;
	}

	/**
	 * Initializing schemas
	 *
	 * @param integer|Configuration $configuration
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function initSchemas($configuration)
	{
		if(false === $configuration)
		{
			throw new Exception('$configuration is false');
		}

		if(!is_object($configuration))
		{
			try
			{
				$storage_configurations = Storage::load('configuration');
			}
			catch(Exception $e)
			{
				throw new Exception('init_schemas: exception - ' . $e->getMessage());
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
				throw new Exception('exception - ' . $e->getMessage());
			}
		}

		if(!$configuration instanceof Configuration)
		{
			throw new Exception('$configuration is not instanceof Configuration');
		}

		try
		{
			$schemas = $this->getSchemas();
		}
		catch(Exception $e)
		{
			throw new Exception('init_schemas: exception - ' . $e->getMessage());
		}

		if(!is_array($schemas))
		{
			throw new Exception('init_schemas: $schemas is not array');
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

		$init_schema->setPrefix(WC1C_PREFIX . 'prefix_' . $schema_id . '_' . $current_configuration_id);
		$init_schema->setConfiguration($configuration);
		$init_schema->setConfigurationPrefix(WC1C_PREFIX . 'configuration_' . $current_configuration_id);

		try
		{
			$init_schema_result = $init_schema->init();
		}
		catch(Exception $e)
		{
			throw new Exception('exception by schema - ' . $e->getMessage());
		}

		if(true !== $init_schema_result)
		{
			throw new Exception('schema is not initialized');
		}

		$init_schema->setInitialized(true);

		return $init_schema;
	}

	/**
	 * Schemas loading
	 *
	 * @throws RuntimeException
	 */
	private function loadSchemas()
	{
		$schemas = [];

		try
		{
			$schema_default = new Schemas\DefaultCML\Init();
		}
		catch(Exception $e)
		{
			throw new RuntimeException('schema init exception - ' . $e->getMessage());
		}

		$schema_default->setId('defaultcml');
		$schema_default->setVersion('0.1.0');
		$schema_default->setName(__('Default schema based on CML', 'wc1c'));
		$schema_default->setDescription(__('Standard data exchange using the standard exchange algorithm from 1C via CommerceML. Exchanges only contains products data.', 'wc1c'));
		$schema_default->setSchemaPrefix(WC1C_PREFIX . 'schema_' . $schema_default->getId());

		$schemas['defaultcml'] = $schema_default;

		/**
		 * External schemas
		 */
		if('yes' === $this->settings()->get('extensions_schemas', 'yes'))
		{
			$schemas = apply_filters(WC1C_PREFIX . 'schemas_loading', $schemas);
		}

		wc1c()->log()->debug('wc1c_schemas_loading $schemas', $schemas);

		try
		{
			$this->setSchemas($schemas);
		}
		catch(Exception $e)
		{
			throw new RuntimeException('exception - ' . $e->getMessage());
		}
	}

	/**
	 * Set schemas
	 *
	 * @param array $schemas
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function setSchemas($schemas)
	{
		if(is_array($schemas))
		{
			$this->schemas = $schemas;
			return true;
		}

		throw new Exception('$schemas is not valid');
	}

	/**
	 * Timer loading
	 *
	 * @return void
	 * @throws Exception
	 */
	private function loadTimer()
	{
		try
		{
			$timer = new Timer();
		}
		catch(Exception $e)
		{
			throw new Exception('load_timer: exception - ' . $e->getMessage());
		}

		$php_max_execution = $this->environment()->get('php_max_execution_time', 20);

		if($this->settings()->get('php_max_execution_time', $php_max_execution) !== $php_max_execution)
		{
			$php_max_execution = $this->settings()->get('php_max_execution_time', $php_max_execution);
		}

		$timer->setMaximum($php_max_execution);

		try
		{
			$this->setTimer($timer);
		}
		catch(Exception $e)
		{
			throw new Exception('load_timer: exception - ' . $e->getMessage());
		}
	}

	/**
	 * @return Timer|null
	 */
	public function timer()
	{
		return $this->timer;
	}

	/**
	 * @param Timer|null $timer
	 */
	public function setTimer($timer)
	{
		$this->timer = $timer;
	}

	/**
	 * Get input
	 *
	 * @return Input
	 */
	public function input()
	{
		return $this->input;
	}

	/**
	 * Set input
	 *
	 * @param Input $input
	 */
	public function setInput($input)
	{
		$this->input = $input;
	}

	/**
	 * Input loading
	 *
	 * @return void
	 * @throws Exception
	 */
	private function loadingInput()
	{
		$default_class_name = 'Input';

		$use_class_name = apply_filters(WC1C_PREFIX . 'input_loading_class_name', $default_class_name);

		if(false === class_exists($use_class_name))
		{
			$this->log()->info(WC1C_PREFIX . 'input_loading_class_name: class is not exists - ' . $use_class_name);
			$use_class_name = $default_class_name;
		}

		try
		{
			$api = new $use_class_name();
		}
		catch(Exception $e)
		{
			throw new Exception('not loaded');
		}

		try
		{
			$this->setInput($api);
		}
		catch(Exception $e)
		{
			throw new Exception('setInput - ' . $e->getMessage());
		}
	}

	/**
	 * @param string $tool_id
	 *
	 * @throws Exception
	 */
	public function initTools($tool_id = '')
	{
		try
		{
			$tool_example = new Wc1c_Tool_Example();
		}
		catch(Exception $e)
		{
			throw new Exception('exception - ' . $e->getMessage());
		}

		$tool_example->set_id('example');
		$tool_example->set_name(__('Example tool', 'wc1c'));
		$tool_example->set_description(__('A demo tool that does not carry any functional load.', 'wc1c'));

		$tools[$tool_example->get_id()] = $tool_example;
	}

	/**
	 * Loading tools
	 *
	 * @return void
	 * @throws Exception
	 */
	public function loadTools()
	{
		/**
		 * key = tool id
		 * value = callback - ToolAbstract
		 */
		$tools =
		[
			'example' => 'Example'
		];

		/**
		 * External tools loading is enabled
		 */
		if('yes' === $this->settings()->get('extensions_tools', 'yes'))
		{
			$tools = apply_filters(WC1C_PREFIX . 'load_tools', $tools);
		}

		try
		{
			$this->setTools($tools);
		}
		catch(Exception $e)
		{
			throw new Exception('exception - ' . $e->getMessage());
		}
	}

	/**
	 * Extensions load
	 *
	 * @return void
	 * @throws Exception
	 */
	private function loadExtensions()
	{
		$extensions = [];

		if('yes' === $this->settings()->get('extensions', 'yes'))
		{
			$extensions = apply_filters(WC1C_PREFIX . 'extensions_loading', $extensions);
		}

		try
		{
			$this->setExtensions($extensions);
		}
		catch(Exception $e)
		{
			throw new Exception('load_extensions: set_extensions - ' . $e->getMessage());
		}
	}

	/**
	 * Get schemas
	 *
	 * @param string $schema_id
	 *
	 * @return array|mixed
	 * @throws RuntimeException
	 */
	public function getSchemas($schema_id = '')
	{
		if('' !== $schema_id)
		{
			if(array_key_exists($schema_id, $this->schemas))
			{
				return $this->schemas[$schema_id];
			}

			throw new RuntimeException('$schema_id is unavailable');
		}

		return $this->schemas;
	}

	/**
	 * Get tools
	 *
	 * @param string $tool_id
	 *
	 * @return array|mixed
	 * @throws Exception
	 */
	public function getTools($tool_id = '')
	{
		if('' !== $tool_id)
		{
			if(array_key_exists($tool_id, $this->tools))
			{
				return $this->tools[$tool_id];
			}

			throw new Exception('$schema_id is unavailable');
		}

		return $this->tools;
	}

	/**
	 * Set tools
	 *
	 * @param array $tools
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setTools($tools)
	{
		if(!is_array($tools))
		{
			throw new Exception('$tools is not valid');
		}

		$this->tools = $tools;
	}

	/**
	 * Get initialized extensions
	 *
	 * @param string $extension_id
	 *
	 * @return array|object
	 * @throws Exception
	 */
	public function getExtensions($extension_id = '')
	{
		if('' !== $extension_id)
		{
			if(array_key_exists($extension_id, $this->extensions))
			{
				return $this->extensions[$extension_id];
			}

			throw new Exception('$extension_id is unavailable');
		}

		return $this->extensions;
	}

	/**
	 * @param array $extensions
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function setExtensions($extensions)
	{
		if(is_array($extensions))
		{
			$this->extensions = $extensions;
			return true;
		}

		throw new Exception('$extensions is not valid');
	}

	/**
	 * Load localisation
	 */
	public function localization()
	{
		/**
		 * WP 5.x or later
		 */
		if(function_exists('determine_locale'))
		{
			$locale = determine_locale();
		}
		else
		{
			$locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
		}

		/**
		 * Change locale from external code
		 */
		$locale = apply_filters('plugin_locale', $locale, 'wc1c');

		unload_textdomain('wc1c');
		load_textdomain('wc1c', WP_LANG_DIR . '/plugins/wc1c-' . $locale . '.mo');
		load_textdomain('wc1c', WC1C_PLUGIN_PATH . 'languages/wc1c-' . $locale . '.mo');
	}

	/**
	 * Use in plugin for DB queries
	 *
	 * @return wpdb
	 */
	public function database()
	{
		global $wpdb;
		return $wpdb;
	}
}