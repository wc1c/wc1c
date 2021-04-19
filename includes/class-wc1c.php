<?php
/**
 * Wc1c class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

final class Wc1c
{
	/**
	 * Traits
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Timer
	 *
	 * @var Wc1c_Timer
	 */
	private $timer = null;

	/**
	 * Logger
	 *
	 * @var Wc1c_Logger
	 */
	private $logger = null;

	/**
	 * Plugin environment
	 *
	 * @var Wc1c_Environment
	 */
	private $environment = null;

	/**
	 * Database
	 *
	 * @var Wc1c_Database
	 */
	private $database = null;

	/**
	 * Base settings
	 *
	 * @var Wc1c_Settings
	 */
	private $settings = null;

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
	 * Plugin api
	 *
	 * @var Wc1c_Api
	 */
	private $api = null;

	/**
	 * All loaded helpers
	 *
	 * @var array
	 */
	private $helpers = [];

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
		$this->init_includes();
		$this->init_hooks();

		wc1c_load_textdomain();

		// hook
		do_action('wc1c_after_loading');
	}

	/**
	 * Include of files
	 *
	 * @return void
	 */
	private function init_includes()
	{
		// hook
		do_action('wc1c_before_includes');

		/**
		 * Exceptions
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/exceptions/class-wc1c-exception.php';
		include_once WC1C_PLUGIN_PATH . 'includes/exceptions/class-wc1c-exception-runtime.php';

		/**
		 * Abstract
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-logger.php';
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-extension.php';
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-schema.php';
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-tool.php';

		/**
		 * Core
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-timer.php';
		include_once WC1C_PLUGIN_PATH . 'includes/functions-wc1c-admin.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-environment.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-logger.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-database.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-configuration.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-settings.php';

		/**
		 * Helpers
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/helpers/class-wc1c-helper-attributes.php';
		include_once WC1C_PLUGIN_PATH . 'includes/helpers/class-wc1c-helper-products.php';
		include_once WC1C_PLUGIN_PATH . 'includes/helpers/class-wc1c-helper-category.php';
		include_once WC1C_PLUGIN_PATH . 'includes/helpers/class-wc1c-helper-images.php';
		include_once WC1C_PLUGIN_PATH . 'includes/helpers/class-wc1c-helper-cml.php';

		/**
		 * Schemas
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/schemas/class-wc1c-schema-logger.php';
		include_once WC1C_PLUGIN_PATH . 'includes/schemas/default/class-wc1c-schema-default.php';

		/**
		 * Standard tools
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/tools/example/class-wc1c-tool-example.php';

		/**
		 * Api
		 */
		if(false !== is_wc1c_api_request())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-api.php';
		}

		/**
		 * Admin
		 */
		if(false !== is_admin())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-admin.php';
		}

		// hook
		do_action('wc1c_after_includes');
	}

	/**
	 * Initializing actions and filters
	 *
	 * @return void
	 */
	private function init_hooks()
	{
		// init
		add_action('init', array($this, 'init'), 3);

		// admin
		if(false !== is_admin())
		{
			add_action('init', array('Wc1c_Admin', 'instance'), 5);
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
			return false;
		}

		try
		{
			$this->load_logger();
		}
		catch(Exception $e)
		{
			return false;
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
			$this->reload_logger();
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
	 * Reload logger
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function reload_logger()
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
	 * Set environment
	 *
	 * @param Wc1c_Environment $environment
	 *
	 * @throws Wc1c_Exception_Runtime
	 *
	 * @return true
	 */
	public function set_environment($environment)
	{
		if($environment instanceof Wc1c_Environment)
		{
			$this->environment = $environment;
			return true;
		}

		throw new Wc1c_Exception_Runtime('set_environment: $environment is not Wc1c_Environment');
	}

	/**
	 * Loading environment
	 *
	 * @return void
	 *
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

		try
		{
			$this->set_environment($environment);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_environment: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Plugin settings loading
	 *
	 * @return void
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_settings()
	{
		try
		{
			$settings = new Wc1c_Settings();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_settings: exception - ' . $e->getMessage());
		}

		try
		{
			$this->set_settings($settings);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_settings: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Get configurations
	 *
	 * @param string $type
	 *  all - all loaded configurations
	 *  current - current configuration
	 *  numeric - configuration identifier
	 *
	 * @return array|Wc1c_Configuration
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function get_configurations($type = 'all')
	{
		if('all' !== $type)
		{
			if('current' === $type)
			{
				$configuration_id = WC1C()->environment()->get('current_configuration_id', 0);

				if(array_key_exists($configuration_id, $this->configurations))
				{
					return $this->configurations[$configuration_id];
				}

				throw new Wc1c_Exception_Runtime('get_configurations: current configuration not loaded');
			}

			if(is_numeric($type) && array_key_exists($type, $this->configurations))
			{
				return $this->configurations[$type];
			}

			throw new Wc1c_Exception_Runtime('get_configurations: configuration by id is not loaded');
		}

		return $this->configurations;
	}

	/**
	 * Initializing extensions
	 * If a extension ID is specified, only the specified extension is loaded
	 *
	 * @param string $extension_id
	 *
	 * @return boolean
	 *
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
	 * If a schema ID is specified, only the specified schema is loaded
	 *
	 * @param string $schema_id
	 *
	 * @return boolean
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function init_schemas($schema_id = '')
	{
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

		/**
		 * Init specified schema
		 */
		if('' !== $schema_id)
		{
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

			$current_configuration_id = WC1C()->environment()->get('current_configuration_id', 0);

			if(0 !== $current_configuration_id)
			{
				$init_schema->set_configuration_prefix('wc1c_configuration_' . $current_configuration_id);
				$init_schema->set_prefix('wc1c_prefix_' . $schema_id . '_' . $current_configuration_id);

				$configuration = $this->get_configurations($current_configuration_id);
				$init_schema->set_configuration($configuration);
			}

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

			return true;
		}

		/**
		 * Init all schemas
		 */
		foreach($schemas as $schema => $schema_data)
		{
			try
			{
				$this->init_schemas($schema);
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
		$plugin_data = get_file_data(WC1C_PLUGIN_FILE, array('Version' => 'Version'));
		wc1c_define('WC1C_PLUGIN_VERSION', $plugin_data['Version']);

		wc1c_define('WC1C_PLUGIN_URL', plugin_dir_url(WC1C_PLUGIN_FILE));
		wc1c_define('WC1C_PLUGIN_NAME', plugin_basename(WC1C_PLUGIN_FILE));
		wc1c_define('WC1C_PLUGIN_PATH', plugin_dir_path(WC1C_PLUGIN_FILE));
	}

	/**
	 * Set plugin settings
	 *
	 * @param $settings
	 *
	 * @return boolean
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_settings($settings)
	{
		if($settings instanceof Wc1c_Settings)
		{
			$this->settings = $settings;
			return true;
		}

		throw new Wc1c_Exception_Runtime('set_settings: $settings is not valid');
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
			$load_configuration = new Wc1c_Configuration();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_configuration: exception - ' . $e->getMessage());
		}

		try
		{
			$load_configuration->set_id($id);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_configuration: exception - ' . $e->getMessage());
		}

		try
		{
			$load_configuration->load();
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
		if($logger instanceof Wc1c_Abstract_Logger)
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
	 * Loading tools
	 *
	 * @param string $tool_id
	 *
	 * @return void
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function load_tools($tool_id = '')
	{
		$tools = [];

		try
		{
			$tool_example = new Wc1c_Tool_Example();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_tools: exception - ' . $e->getMessage());
		}

		$tool_example->set_id('example');
		$tool_example->set_version(WC1C_PLUGIN_VERSION);
		$tool_example->set_name(__('Example tool', 'wc1c'));
		$tool_example->set_description(__('A demo tool that does not carry any functional load.', 'wc1c'));

		$tools[$tool_example->get_id()] = $tool_example;

		/**
		 * External tools loading is enable
		 */
		if('yes' === $this->settings()->get('extensions_tools', 'yes'))
		{
			$tools = apply_filters('wc1c_tools_loading', $tools);
		}

		WC1C()->logger()->debug('load_tools: $tools', $tools);

		try
		{
			$this->set_tools($tools);
		}
		catch(Exception $e)
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

		WC1C()->logger()->debug('load_extensions: $extensions', $extensions);

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
	 *
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
	 * @return boolean
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_tools($tools)
	{
		if(is_array($tools))
		{
			$this->tools = $tools;
			return true;
		}

		throw new Wc1c_Exception_Runtime('set_tools: $tools is not valid');
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

	/**
	 * @param string $helper_id
	 *
	 * @return void
	 * @throws Wc1c_Exception_Runtime
	 */
	private function load_helpers($helper_id = '')
	{
		try
		{
			$helpers = $this->get_helpers();
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_helpers: exception - ' . $e->getMessage());
		}

		$available_helpers = [
			'cml' => 'Wc1c_Helper_Cml',
			'products' => 'Wc1c_Helper_Products',
			'attributes' => 'Wc1c_Helper_Attributes',
			'category' => 'Wc1c_Helper_Category',
			'images' => 'Wc1c_Helper_Images'
		];

		if(!array_key_exists($helper_id, $available_helpers))
		{
			throw new Wc1c_Exception_Runtime('load_helpers: helper is unavailable by id - ' . $helper_id);
		}

		$helpers[$helper_id] = new $available_helpers[$helper_id]();

		try
		{
			$this->set_helpers($helpers);
		}
		catch(Exception $e)
		{
			throw new Wc1c_Exception_Runtime('load_helpers: exception - ' . $e->getMessage());
		}
	}

	/**
	 * @param string $helper_id
	 *
	 * @return array|mixed
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function get_helpers($helper_id = '')
	{
		if('' !== $helper_id)
		{
			if(array_key_exists($helper_id, $this->helpers))
			{
				return $this->helpers[$helper_id];
			}

			try
			{
				$this->load_helpers($helper_id);
			}
			catch(Exception $e)
			{
				throw new Wc1c_Exception_Runtime('get_helpers: $helper_id is unavailable');
			}

			return $this->helpers[$helper_id];
		}

		return $this->helpers;
	}

	/**
	 * @param array $helpers
	 *
	 * @return bool
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function set_helpers($helpers)
	{
		if(is_array($helpers))
		{
			$this->helpers = $helpers;
			return true;
		}

		throw new Wc1c_Exception_Runtime('set_helpers: $helpers is not valid');
	}
}