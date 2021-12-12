<?php
/**
 * Wc1c class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

final class Wc1c implements Interface_Wc1c
{
	/**
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Timer
	 *
	 * @var Wc1c_Timer
	 */
	private $timer;

	/**
	 * Logger
	 *
	 * @var Wc1c_Logger
	 */
	private $logger;

	/**
	 * Plugin environment
	 *
	 * @var Wc1c_Environment
	 */
	private $environment;

	/**
	 * Plugin settings
	 *
	 * @var Wc1c_Settings
	 */
	private $settings;

	/**
	 * Database
	 *
	 * @var Wc1c_Database
	 */
	private $database;

	/**
	 * Plugin api
	 *
	 * @var Wc1c_Api
	 */
	private $api;

	/**
	 * All loaded configurations
	 *
	 * @var array
	 */
	private $configurations = [];

	/**
	 * All loaded extensions
	 *
	 * @var array
	 */
	private $extensions = [];

	/**
	 * All loaded schemas
	 *
	 * @var array
	 */
	private $schemas = [];

	/**
	 * All loaded tools
	 *
	 * @var array
	 */
	private $tools = [];

	/**
	 * Wc1c constructor
	 *
	 * @return void
	 */
	public function __construct()
	{
		// hook
		do_action('wc1c_before_loading');

		$this->define_constants();
		$this->init_hooks();

		wc1c_load_textdomain();

		// hook
		do_action('wc1c_after_loading');
	}

	/**
	 * Initializing actions and filters
	 *
	 * @return void
	 */
	private function init_hooks()
	{
		// init
		add_action('init', [$this, 'init'], 3);

		// admin
		if(false !== is_admin())
		{
			add_action('init', ['Wc1c_Admin', 'instance'], 5);
		}
	}

	/**
	 * Initialization
	 *
	 * @return bool
	 */
	public function init()
	{
		// hook
		do_action('wc1c_before_init');

		try
		{
			$this->load_environment();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('init: exception - ' . $e->getMessage());
		}

		try
		{
			$this->load_logger();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('init: exception - ' . $e->getMessage());
		}

		try
		{
			$this->load_settings();
		}
		catch(Exception $e)
		{
			WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->load_timer();
		}
		catch(Exception $e)
		{
			WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->init_logger();
		}
		catch(Exception $e)
		{
			WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->load_extensions();
		}
		catch(Exception $e)
		{
			WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->init_extensions();
		}
		catch(Exception $e)
		{
			WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->load_schemas();
		}
		catch(Exception $e)
		{
			WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
			return false;
		}

		if(false !== is_wc1c_api_request() || false !== is_wc1c_admin_request())
		{
			try
			{
				$this->load_tools();
			}
			catch(Exception $e)
			{
				WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
				return false;
			}
		}

		if(false !== is_wc1c_api_request())
		{
			try
			{
				$this->load_api();
			}
			catch(Exception $e)
			{
				WC1C()->logger()->alert('init: exception - ' . $e->getMessage());
				return false;
			}
		}

		// hook
		do_action('wc1c_after_init');

		return true;
	}

	/**
	 * Init logger
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function init_logger()
	{
		$logger_level = $this->settings()->get('logger', 400);

		if('' === $logger_level)
		{
			$logger_level = 100;
		}

		WC1C()->logger()->set_level($logger_level);

		$directory_name = $this->settings()->get('upload_directory_name', 'wc1c');

		$logger_path = $this->environment()->get('upload_directory') . DIRECTORY_SEPARATOR . $directory_name;

		$this->environment()->set('wc1c_upload_directory', $logger_path);

		WC1C()->logger()->set_path($logger_path);
		WC1C()->logger()->set_name('wc1c.main.log');

		return true;
	}

	/**
	 * Get environment
	 *
	 * @return Wc1c_Environment
	 */
	public function environment()
	{
		return $this->environment;
	}

	/**
	 * Get settings
	 *
	 * @return Wc1c_Settings
	 */
	public function settings()
	{
		return $this->settings;
	}

	/**
	 * Loading environment
	 *
	 * @return void
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_environment()
	{
		try
		{
			$environment = new Wc1c_Environment();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_environment: exception - ' . $e->getMessage());
		}

		$this->environment = $environment;
	}

	/**
	 * Load main settings
	 *
	 * @return void
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_settings()
	{
		try
		{
			$settings = new Wc1c_Settings();
			$settings->init();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_settings: exception - ' . $e->getMessage());
		}

		if(!$settings instanceof Interface_Wc1c_Settings)
		{
			throw new Wc1c_Exception_Runtime('set_settings: $settings is not valid');
		}

		$this->settings = $settings;
	}

	/**
	 * Initializing extensions
	 * If a extension ID is specified, only the specified extension is loaded
	 *
	 * @param string $extension_id
	 *
	 * @return boolean
	 * @throws Wc1c_Exception_Runtime
	 */
	public function init_extensions($extension_id = '')
	{
		try
		{
			$extensions = $this->get_extensions();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('init_extensions: $extensions - ' . $e->getMessage());
		}

		if(!is_array($extensions))
		{
			throw new Wc1c_Exception_Runtime('init_extensions: $extensions is not array');
		}

		/**
		 * Init specified extension
		 */
		if('' !== $extension_id)
		{
			if(!array_key_exists($extension_id, $extensions))
			{
				throw new Wc1c_Exception_Runtime('init_extensions: extension not found by id');
			}

			$init_extension = $extensions[$extension_id];

			if(!is_object($init_extension))
			{
				throw new Wc1c_Exception_Runtime('init_extensions: $extensions[$extension_id] is not object');
			}

			if($init_extension->is_initialized())
			{
				throw new Wc1c_Exception_Runtime('init_extensions: old initialized');
			}

			$areas = $init_extension->get_areas();
			if(false === $this->validate_areas($areas))
			{
				return true;
			}

			if(!method_exists($init_extension, 'init'))
			{
				throw new Wc1c_Exception_Runtime('init_extensions: method init not found');
			}

			try
			{
				$init_extension->init();
			}
			catch(Exception $e)
			{
				throw new Wc1c_Exception_Runtime('init_extensions: exception by extension - ' . $e->getMessage());
			}

			$init_extension->set_initialized(true);

			return true;
		}

		/**
		 * Init all extensions
		 */
		foreach($extensions as $extension => $extension_object)
		{
			try
			{
				$this->init_extensions($extension);
			}
			catch(Exception $e)
			{
				WC1C()->logger()->error($e->getMessage(), $e);
				continue;
			}
		}

		return true;
	}

	/**
	 * @param array $areas
	 *
	 * @return bool
	 */
	public function validate_areas($areas = ['any'])
	{
		if(!is_array($areas))
		{
			return false;
		}

		/**
		 * Any
		 */
		if(in_array('any', $areas, true))
		{
			return true;
		}

		/**
		 * Admin
		 */
		if(in_array('admin', $areas, true) && is_admin())
		{
			return true;
		}

		/**
		 * Site
		 */
		if(in_array('site', $areas, true) && !is_admin())
		{
			return true;
		}

		/**
		 * Wc1c admin
		 */
		if(in_array('wc1c_admin', $areas, true) && is_wc1c_admin_request())
		{
			return true;
		}

		/**
		 * Wc1c api
		 */
		if(in_array('wc1c_api', $areas, true) && is_wc1c_api_request())
		{
			return true;
		}

		return false;
	}

	/**
	 * Initializing schemas
	 *
	 * @param integer|Wc1c_Configuration $configuration
	 *
	 * @return boolean
	 * @throws Wc1c_Exception_Runtime
	 */
	public function init_schemas($configuration)
	{
		if(false === $configuration)
		{
			throw new Wc1c_Exception_Runtime('init_schemas: $configuration is false');
		}

		if(!is_object($configuration))
		{
			try
			{
				$storage_configurations = Wc1c_Data_Storage::load('configuration');
			}
			catch(Exception $e)
			{
				throw new Wc1c_Exception_Runtime('init_schemas: exception - ' . $e->getMessage());
			}

			if(!$storage_configurations->is_existing_by_id($configuration))
			{
				throw new Wc1c_Exception_Runtime('init_schemas: $configuration is not exists');
			}

			try
			{
				$configuration = new Wc1c_Configuration($configuration);
			}
			catch(Exception $e)
			{
				throw new Wc1c_Exception_Runtime('init_schemas: exception - ' . $e->getMessage());
			}
		}

		if(!$configuration instanceof Wc1c_Configuration)
		{
			throw new Wc1c_Exception_Runtime('init_schemas: $configuration is not instanceof Wc1c_Configuration');
		}

		try
		{
			$schemas = $this->get_schemas();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('init_schemas: exception - ' . $e->getMessage());
		}

		if(!is_array($schemas))
		{
			throw new Wc1c_Exception_Runtime('init_schemas: $schemas is not array');
		}

		$schema_id = $configuration->get_schema();

		if(!array_key_exists($schema_id, $schemas))
		{
			throw new Wc1c_Exception_Runtime('init_schemas: schema not found by id: ' . $schema_id);
		}

		if(!is_object($schemas[$schema_id]))
		{
			throw new Wc1c_Exception_Runtime('init_schemas: $schemas[$schema_id] is not object');
		}

		$init_schema = $schemas[$schema_id];

		if($init_schema->is_initialized())
		{
			throw new Wc1c_Exception_Runtime('init_schemas: old initialized, $schema_id: ' . $schema_id);
		}

		if(!method_exists($init_schema, 'init'))
		{
			throw new Wc1c_Exception_Runtime('init_schemas: method init not found, $schema_id: ' . $schema_id);
		}

		$current_configuration_id = $configuration->get_id();

		$init_schema->set_prefix('wc1c_prefix_' . $schema_id . '_' . $current_configuration_id);
		$init_schema->set_configuration($configuration);
		$init_schema->set_configuration_prefix('wc1c_configuration_' . $current_configuration_id);

		try
		{
			$init_schema_result = $init_schema->init();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('init_schemas: exception by schema - ' . $e->getMessage());
		}

		if(true !== $init_schema_result)
		{
			throw new Wc1c_Exception_Runtime('init_schemas: schema is not initialized');
		}

		$init_schema->set_initialized(true);

		return $init_schema;
	}

	/**
	 * Schemas loading
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_schemas()
	{
		$schemas = [];

		try
		{
			$schema_default = new Wc1c_Schema_Default();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_schemas: schema exception - ' . $e->getMessage());
		}

		$schema_default->set_id('default');
		$schema_default->set_version(WC1C_PLUGIN_VERSION);
		$schema_default->set_name(__('Default schema', 'wc1c'));
		$schema_default->set_description(__('Standard data exchange using the standard exchange algorithm from 1C via CommerceML. Exchanges only contains products data.', 'wc1c'));
		$schema_default->set_schema_prefix('wc1c_schema_' . $schema_default->get_id());

		$schemas['default'] = $schema_default;

		/**
		 * External schemas
		 */
		if('yes' === $this->settings()->get('extensions_schemas', 'yes'))
		{
			$schemas = apply_filters('wc1c_schemas_loading', $schemas);
		}

		WC1C()->logger()->debug('load_schemas: wc1c_schemas_loading $schemas', $schemas);

		try
		{
			$this->set_schemas($schemas);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_schemas: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Set schemas
	 *
	 * @param array $schemas
	 *
	 * @return boolean
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_schemas($schemas)
	{
		if(is_array($schemas))
		{
			$this->schemas = $schemas;
			return true;
		}

		throw new Wc1c_Exception_Runtime('set_schemas: $schemas is not valid');
	}

	/**
	 * Define constants
	 *
	 * @return void
	 */
	private function define_constants()
	{
		$plugin_data = get_file_data(WC1C_PLUGIN_FILE, ['Version' => 'Version']);

		define('WC1C_PLUGIN_VERSION', $plugin_data['Version']);
	}

	/**
	 * Configuration loading
	 *
	 * @param bool $id
	 * @param bool $reload
	 *
	 * @return Wc1c_Configuration
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function load_configuration($id = false, $reload = false)
	{
		try
		{
			$configurations = $this->get_configurations();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_configuration: exception - ' . $e->getMessage());
		}

		if(false === $id)
		{
			throw new Wc1c_Exception_Runtime('load_configurations: $id is not exists');
		}

		if(array_key_exists($id, $configurations) && false === $reload)
		{
			throw new Wc1c_Exception_Runtime('load_configurations: $id is exists & $reload false');
		}

		try
		{
			$load_configuration = new Wc1c_Configuration($id);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_configuration: exception - ' . $e->getMessage());
		}

		$load_configuration = apply_filters('wc1c_configuration_load', $load_configuration);

		$configurations[$id] = $load_configuration;

		try
		{
			$this->set_configurations($configurations);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_configurations: exception - ' . $e->getMessage());
		}

		return $load_configuration;
	}

	/**
	 * Set configurations
	 * 
	 * @param $configurations
	 * @param $append
	 *
	 * @return boolean
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_configurations($configurations, $append = false)
	{
		if(is_array($configurations))
		{
			if(true === $append)
			{
				$configurations = array_merge($this->get_configurations('all'), $configurations);
			}

			$this->configurations = $configurations;
			return true;
		}

		throw new Wc1c_Exception_Runtime('set_configurations: $configurations is not valid');
	}

	/**
	 * Logger loading
	 *
	 * @return void
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_logger()
	{
		$directory = $this->environment()->get('upload_directory', false);

		if(false === $directory)
		{
			throw new Wc1c_Exception_Runtime('WordPress upload directory not found');
		}

		try
		{
			$logger = new Wc1c_Logger(50, 'wc1c.boot.log');
			$logger->set_path($directory);
			$logger->init();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_logger: exception - ' . $e->getMessage());
		}

		try
		{
			$this->set_logger($logger);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_logger: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Timer loading
	 *
	 * @return void
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_timer()
	{
		try
		{
			$timer = new Wc1c_Timer();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_timer: exception - ' . $e->getMessage());
		}

		$php_max_execution = $this->environment()->get('php_max_execution_time', 20);

		if($this->settings()->get('php_max_execution_time', $php_max_execution) !== $php_max_execution)
		{
			$php_max_execution = $this->settings()->get('php_max_execution_time', $php_max_execution);
		}

		$timer->set_maximum($php_max_execution);

		try
		{
			$this->set_timer($timer);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_timer: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Set logger
	 *
	 * @param $logger
	 *
	 * @return $this
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_logger($logger)
	{
		if($logger instanceof Abstract_Wc1c_Logger)
		{
			$this->logger = $logger;
			return $this;
		}

		throw new Wc1c_Exception_Runtime('set_logger: $logger is not valid');
	}

	/**
	 * @return Wc1c_Timer|null
	 */
	public function timer()
	{
		return $this->timer;
	}

	/**
	 * @param Wc1c_Timer|null $timer
	 */
	public function set_timer($timer)
	{
		$this->timer = $timer;
	}

	/**
	 * Get logger
	 *
	 * @return Wc1c_Logger
	 */
	public function logger()
	{
		return $this->logger;
	}

	/**
	 * Get API
	 *
	 * @return Wc1c_Api
	 */
	public function api()
	{
		return $this->api;
	}

	/**
	 * Set API
	 *
	 * @param Wc1c_Api $api
	 */
	public function set_api($api)
	{
		$this->api = $api;
	}

	/**
	 * API loading
	 *
	 * @return void
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_api()
	{
		$default_class_name = 'Wc1c_Api';

		$wc1c_api_class_name = apply_filters('wc1c_api_loading_class_name', $default_class_name);

		if(false === class_exists($wc1c_api_class_name))
		{
			$wc1c_api_class_name = $default_class_name;
		}

		try
		{
			$api = new $wc1c_api_class_name();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_api: not loaded');
		}

		try
		{
			$this->set_api($api);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_api: exception - ' . $e->getMessage());
		}
	}

	/**
	 * @param string $tool_id
	 */
	public function init_tools($tool_id = '')
	{

		if(!empty($tool_id) && !array_key_exists($tool_id, $this->get_tools()))
		{

		}

		try
		{
			$tool_example = new Wc1c_Tool_Example();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_tools: exception - ' . $e->getMessage());
		}

		$tool_example->set_id('example');
		$tool_example->set_name(__('Example tool', 'wc1c'));
		$tool_example->set_description(__('A demo tool that does not carry any functional load.', 'wc1c'));

		$tools[$tool_example->get_id()] = $tool_example;

		// TODO: Implement init_tools() method.
	}

	/**
	 * Loading tools
	 *
	 * @return void
	 * @throws Wc1c_Exception_Runtime
	 */
	public function load_tools()
	{
		/**
		 * key = tool id
		 * value = callback (Abstract_Wc1c_Tool)
		 */
		$tools =
		[
			'example' => 'Wc1c_Tool_Example'
		];

		/**
		 * External tools loading is enable
		 */
		if('yes' === $this->settings()->get('extensions_tools', 'yes'))
		{
			$tools = apply_filters('wc1c_load_tools', $tools);
		}

		try
		{
			$this->set_tools($tools);
		}
		catch(Wc1c_Exception_Runtime $e)
		{
			throw new Wc1c_Exception_Runtime('load_tools: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Extensions load
	 *
	 * @return void
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_extensions()
	{
		$extensions = [];

		if('yes' === $this->settings()->get('extensions', 'yes'))
		{
			$extensions = apply_filters('wc1c_extensions_loading', $extensions);
		}

		try
		{
			$this->set_extensions($extensions);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_extensions: set_extensions - ' . $e->getMessage());
		}
	}

	/**
	 * Get schemas
	 *
	 * @param string $schema_id
	 *
	 * @return array|mixed
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function get_schemas($schema_id = '')
	{
		if('' !== $schema_id)
		{
			if(array_key_exists($schema_id, $this->schemas))
			{
				return $this->schemas[$schema_id];
			}

			throw new Wc1c_Exception_Runtime('get_schemas: $schema_id is unavailable');
		}

		return $this->schemas;
	}

	/**
	 * Get tools
	 *
	 * @param string $tool_id
	 *
	 * @return array|mixed
	 * @throws Wc1c_Exception_Runtime
	 */
	public function get_tools($tool_id = '')
	{
		if('' !== $tool_id)
		{
			if(array_key_exists($tool_id, $this->tools))
			{
				return $this->tools[$tool_id];
			}

			throw new Wc1c_Exception_Runtime('get_tools: $schema_id is unavailable');
		}

		return $this->tools;
	}

	/**
	 * Set tools
	 *
	 * @param array $tools
	 *
	 * @return void
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_tools($tools)
	{
		if(!is_array($tools))
		{
			throw new Wc1c_Exception_Runtime('set_tools: $tools is not valid');
		}

		$this->tools = $tools;
	}

	/**
	 * Get initialized extensions
	 *
	 * @param string $extension_id
	 *
	 * @return array|object
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function get_extensions($extension_id = '')
	{
		if('' !== $extension_id)
		{
			if(array_key_exists($extension_id, $this->extensions))
			{
				return $this->extensions[$extension_id];
			}

			throw new Wc1c_Exception_Runtime('get_extensions: $extension_id is unavailable');
		}

		return $this->extensions;
	}

	/**
	 * @param array $extensions
	 *
	 * @return bool
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_extensions($extensions)
	{
		if(is_array($extensions))
		{
			$this->extensions = $extensions;
			return true;
		}

		throw new Wc1c_Exception_Runtime('set_extensions: $extensions is not valid');
	}
}