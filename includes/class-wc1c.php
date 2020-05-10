<?php
/**
 * Final main Wc1c class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

final class Wc1c
{
	/**
	 * The single instance of the class
	 *
	 * @var Wc1c
	 */
	protected static $_instance = null;

	/**
	 * Logger
	 *
	 * @var null|Wc1c_Logger
	 */
	private $logger = null;

	/**
	 * Plugin environment
	 *
	 * @var null|Wc1c_Environment
	 */
	private $environment = null;

	/**
	 * Base settings
	 *
	 * @var null|Wc1c_Settings
	 */
	private $settings = null;

	/**
	 * All configurations
	 *
	 * @var array
	 */
	private $configurations = [];

	/**
	 * All extensions
	 *
	 * @var array
	 */
	private $extensions = [];

	/**
	 * All schemas
	 *
	 * @var array
	 */
	private $schemas = [];

	/**
	 * All tools
	 *
	 * @var array
	 */
	private $tools = [];

	/**
	 * Plugin api
	 *
	 * @var null|Wc1c_Api
	 */
	private $api = null;

	/**
	 * Main Wc1c instance
	 *
	 * @return Wc1c
	 */
	public static function instance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Wc1c constructor
	 */
	public function __construct()
	{
		// hook
		do_action('wc1c_before_loading');

		$this->define_constants();

		wc1c_load_textdomain();

		$this->init_includes();
		$this->init_hooks();

		// hook
		do_action('wc1c_after_loading');
	}

	/**
	 * Include of files
	 */
	private function init_includes()
	{
		// hook
		do_action('wc1c_before_includes');

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
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-environment.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-logger.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-configuration.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-settings.php';

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
	 */
	private function init_hooks()
	{
		// init
		add_action('init', array($this, 'init'), 3);

		/*
		 * activation\deactivation
		 */
		register_activation_hook(WC1C_PLUGIN_FILE, 'wc1c_install');

		// admin
		if(false !== is_admin())
		{
			add_action('init', array('Wc1c_Admin', 'instance'), 5);
		}
	}

	/**
	 * Initialization
	 *
	 * @return void|false
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
			$this->load_api();
		}

		// hook
		do_action('wc1c_after_init');
	}

	/**
	 * Reload logger
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function reload_logger()
	{
		$logger_level = $this->settings()->get('logger', 400);
		$directory_name = $this->settings()->get('upload_directory_name', 'wc1c');

		$logger_path = $this->environment()->get('upload_directory') . DIRECTORY_SEPARATOR . $directory_name;
		$logger_name = 'wc1c.main.log';

		$this->environment()->set('wc1c_upload_directory', $logger_path);

		if($logger_level && $directory_name)
		{
			WC1C()->logger()->set_level($logger_level);
			WC1C()->logger()->set_path($logger_path);
			WC1C()->logger()->set_name($logger_name);
		}
		else
		{
			throw new Exception('reload_logger: error');
		}

		return true;
	}

	/**
	 * Get environment
	 *
	 * @return null|Wc1c_Environment
	 */
	public function environment()
	{
		return $this->environment;
	}

	/**
	 * Get settings
	 *
	 * @return null|Wc1c_Settings
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
	 * @throws Exception
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

		throw new Exception('set_environment: $environment is not Wc1c_Environment');
	}

	/**
	 * Loading environment
	 *
	 * @throws Exception
	 */
	public function load_environment()
	{
		try
		{
			$environment = new Wc1c_Environment();
		}
		catch(Exception $e)
		{
			throw new Exception('load_environment: exception - ' . $e->getMessage());
		}

		try
		{
			$this->set_environment($environment);
		}
		catch(Exception $e)
		{
			throw new Exception('load_environment: exception - ' . $e->getMessage());
		}
	}

	/**
	 * Plugin settings loading
	 *
	 * @throws Exception
	 */
	public function load_settings()
	{
		try
		{
			$settings = new Wc1c_Settings();
		}
		catch(Exception $e)
		{
			throw new Exception('load_settings: exception - ' . $e->getMessage());
		}

		try
		{
			$this->set_settings($settings);
		}
		catch(Exception $e)
		{
			throw new Exception('load_settings: exception - ' . $e->getMessage());
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
	 * @return array|boolean
	 */
	public function get_configurations($type = 'all')
	{
		if($type !== 'all')
		{
			/**
			 * Current
			 */
			if($type === 'current')
			{
				$configuration_id = WC1C()->environment()->get('current_configuration_id', 0);

				if(array_key_exists($configuration_id, $this->configurations))
				{
					return $this->configurations[$configuration_id];
				}

				return false;
			}

			/**
			 * By id
			 */
			if(is_numeric($type) && array_key_exists($type, $this->configurations))
			{
				return $this->configurations[$type];
			}

			return false;
		}

		return $this->configurations;
	}

	/**
	 * Initializing configurations
	 *
	 * If a schema ID is specified, only the specified configuration is loaded
	 *
	 * @param string $configuration_id
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function init_configurations($configuration_id = '')
	{
		/**
		 * Get all loaded configurations
		 */
		$configurations = $this->get_configurations();

		/**
		 * Invalid schemas
		 */
		if(!is_array($configurations))
		{
			return false;
		}

		/**
		 * Init specified configuration
		 */
		if($configuration_id !== '')
		{
			/**
			 * Configuration not exists
			 */
			if(!array_key_exists($configuration_id, $configurations))
			{
				return false;
			}

			/**
			 * Configuration initialized
			 */
			if(is_object($configurations[$configuration_id]['instance']))
			{
				return true;
			}

			/**
			 * Create object & save to buffer
			 */
			$configurations[$configuration_id]['instance'] = new Wc1c_Configuration($configurations[$configuration_id][0]);

			/**
			 * Reload buffer
			 */
			$this->set_configurations($configurations);

			/**
			 * Success
			 */
			return true;
		}

		/**
		 * Init all configurations
		 */
		$return = true;
		foreach($configurations as $configuration_id => $schema_data)
		{
			$result = $this->init_configurations($configuration_id);
			if($result === false)
			{
				$return = false;
			}
		}
		return $return;
	}

	/**
	 * Initializing extensions
	 * If a extension ID is specified, only the specified extension is loaded
	 *
	 * @param string $extension_id
	 *
	 * @return boolean
	 * @throws Exception
	 */
	public function init_extensions($extension_id = '')
	{
		/**
		 * Get all loaded extensions
		 */
		$extensions = $this->get_extensions();

		/**
		 * Invalid extensions
		 */
		if(!is_array($extensions))
		{
			throw new Exception('init_extensions: $extensions is not array');
		}

		/**
		 * Init specified extension
		 */
		if($extension_id !== '')
		{
			/**
			 * Extension not exists
			 */
			if(!array_key_exists($extension_id, $extensions))
			{
				throw new Exception('init_extensions: extension not found by id');
			}

			$init_extension = $extensions[$extension_id];

			/**
			 * Extension validate
			 */
			if(!is_object($init_extension))
			{
				throw new Exception('init_extensions: $extensions[$extension_id] is not object');
			}

			/**
			 * Extension initialized
			 */
			if($init_extension->is_initialized())
			{
				throw new Exception('init_extensions: old initialized');
			}

			/**
			 * Valid areas
			 */
			$areas = $init_extension->get_areas();
			if(false === $this->validate_areas($areas))
			{
				return true;
			}

			/**
			 * Init method not found
			 */
			if(!method_exists($init_extension, 'init'))
			{
				throw new Exception('init_extensions: method init not found');
			}

			/**
			 * Init
			 */
			try
			{
				$init_extension->init();
			}
			catch(Exception $e)
			{
				throw new Exception('init_extensions: exception by extension - ' . $e->getMessage());
			}

			$init_extension->set_initialized(true);

			return true;
		}

		/**
		 * Init all extensions
		 */
		foreach($extensions as $extension_id => $extension_object)
		{
			try
			{
				$this->init_extensions($extension_id);
			}
			catch(Exception $exception)
			{
				//todo: log
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
		if(in_array('any', $areas))
		{
			return true;
		}

		/**
		 * Admin
		 */
		if(in_array('admin', $areas) && is_admin())
		{
			return true;
		}

		/**
		 * Site
		 */
		if(in_array('site', $areas) && !is_admin())
		{
			return true;
		}

		/**
		 * Wc1c admin
		 */
		if(in_array('wc1c_admin', $areas) && is_wc1c_admin_request())
		{
			return true;
		}

		/**
		 * Wc1c api
		 */
		if(in_array('wc1c_api', $areas) && is_wc1c_api_request())
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
	 * @throws Exception
	 * @return boolean
	 */
	public function init_schemas($schema_id = '')
	{
		/**
		 * Get all loaded schemas
		 */
		$schemas = $this->get_schemas();

		/**
		 * Invalid schemas
		 */
		if(!is_array($schemas))
		{
			throw new Exception('init_schemas: $schemas is not array');
		}

		/**
		 * Init specified schema
		 */
		if($schema_id !== '')
		{
			/**
			 * Schema not exists
			 */
			if(!array_key_exists($schema_id, $schemas))
			{
				throw new Exception('init_schemas: extension not found by id');
			}

			/**
			 * Schema validate
			 */
			if(!is_object($schemas[$schema_id]))
			{
				throw new Exception('init_schemas: $extensions[$extension_id] is not object');
			}

			$init_schema = $schemas[$schema_id];

			/**
			 * Schema initialized
			 */
			if($init_schema->is_initialized())
			{
				throw new Exception('init_schemas: old initialized');
			}

			/**
			 * Init method not found
			 */
			if(!method_exists($init_schema, 'init'))
			{
				throw new Exception('init_schemas: method init not found');
			}

			try
			{
				$configuration_id = WC1C()->environment()->get('current_configuration_id', 0);

				if($configuration_id !== 0)
				{
					$options = $this->get_configurations('current');

					$init_schema->set_options($options['instance']->get_options());
					$init_schema->set_configuration_prefix('wc1c_configuration_' . $configuration_id);
					$init_schema->set_prefix('wc1c_prefix_' . $schema_id . '_' . $configuration_id);
				}

				$init_schema->init();
			}
			catch(Exception $e)
			{
				throw new Exception('init_schemas: exception by schema - ' . $e->getMessage());
			}

			$init_schema->set_initialized(true);

			return true;
		}

		/**
		 * Init all schemas
		 */
		foreach($schemas as $schema_id => $schema_data)
		{
			try
			{
				$this->init_schemas($schema_id);
			}
			catch(Exception $e)
			{
				continue;
			}
		}

		return true;
	}

	/**
	 * Schemas loading
	 *
	 * @return array - loaded schemas
	 */
	public function load_schemas()
	{
		$schemas = [];

		try
		{
			$schema_default = new Wc1c_Schema_Default();

			$schema_default->set_id('default');
			$schema_default->set_version(WC1C_VERSION);
			$schema_default->set_name(__('Default schema', 'wc1c'));
			$schema_default->set_description(__('Стандартный обмен данными по стандатному алгоритму обмена от 1С через CommerceML. В обмене только данные по товарам.', 'wc1c'));
			$schema_default->set_schema_prefix('wc1c_schema_' . $schema_default->get_id());

			$schemas['default'] = $schema_default;
		}
		catch(Exception $e)
		{
			//todo: exception
		}

		/**
		 * External schemas
		 */
		if('yes' === $this->settings()->get('extensions_schemas'))
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
			//todo: exception
		}

		return $this->get_schemas();
	}

	/**
	 * Set schemas
	 *
	 * @param array $schemas
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function set_schemas($schemas)
	{
		if(is_array($schemas))
		{
			$this->schemas = $schemas;

			return true;
		}

		throw new Exception('set_schemas: $schemas is not valid');
	}

	/**
	 * Define constants
	 */
	private function define_constants()
	{
		$plugin_data = get_file_data(WC1C_PLUGIN_FILE, array('Version' => 'Version'));
		wc1c_define('WC1C_VERSION', $plugin_data['Version']);

		wc1c_define('WC1C_PLUGIN_URL', plugin_dir_url(WC1C_PLUGIN_FILE));
		wc1c_define('WC1C_PLUGIN_NAME', plugin_basename(WC1C_PLUGIN_FILE));
		wc1c_define('WC1C_PLUGIN_PATH', plugin_dir_path(WC1C_PLUGIN_FILE));
	}

	/**
	 * Set plugin settings
	 *
	 * @param $settings
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function set_settings($settings)
	{
		if($settings instanceof Wc1c_Settings)
		{
			$this->settings = $settings;

			return true;
		}

		throw new Exception('set_settings: $settings is not valid');
	}

	/**
	 * Configurations loading
	 *
	 * @param bool $id
	 *
	 * @return bool
	 */
	public function load_configurations($id = false)
	{
		$configurations = [];

		$config_query = 'SELECT * from ' . WC1C_Db()->base_prefix . 'wc1c';
		if($id !== false)
		{
			$config_query .= ' WHERE config_id = ' . $id;
		}

		$config_results = WC1C_Db()->get_results($config_query, ARRAY_A); // todo: cache

		foreach($config_results as $config_key => $config_value)
		{
			if(!is_array($config_value) || !array_key_exists('config_id', $config_value))
			{
				continue;
			}

			$config_position = array
			(
				'instance' => null
			);
			$config_position = array_merge($config_results, $config_position);

			$config_position = apply_filters('wc1c_configurations_load_position', $config_position);

			$configurations[$config_value['config_id']] = $config_position;
		}

		$configurations = apply_filters('wc1c_configurations_load', $configurations);

		$this->set_configurations($configurations);

		return true;
	}

	/**
	 * Set configurations
	 * 
	 * @param $configurations
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function set_configurations($configurations)
	{
		if(is_array($configurations))
		{
			$this->configurations = $configurations;

			return true;
		}

		throw new Exception('set_configurations: $configurations is not valid');
	}

	/**
	 * Logger loading
	 *
	 * @throws Exception
	 *
	 * @return boolean
	 */
	public function load_logger()
	{
		$directory = $this->environment()->get('upload_directory');

		if(false === $directory)
		{
			throw new Exception('WordPress upload directory not found');
		}

		$logger = new Wc1c_Logger($directory,50, 'wc1c.boot.log');

		$this->set_logger($logger);

		return true;
	}

	/**
	 * Set logger
	 *
	 * @param $logger
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function set_logger($logger)
	{
		if($logger instanceof Wc1c_Abstract_Logger)
		{
			$this->logger = $logger;

			return $this;
		}

		throw new Exception('set_logger: $logger is not valid');
	}

	/**
	 * Get logger
	 *
	 * @return Wc1c_Logger|null
	 */
	public function logger()
	{
		return $this->logger;
	}

	/**
	 * Get API
	 *
	 * @return null|Wc1c_Api
	 */
	public function api()
	{
		return $this->api;
	}

	/**
	 * Set API
	 *
	 * @param null|Wc1c_Api $api
	 */
	public function set_api($api)
	{
		$this->api = $api;
	}

	/**
	 * API loading
	 */
	public function load_api()
	{
		/**
		 * Default API class name
		 */
		$default_class_name = 'Wc1c_Api';

		/**
		 * Change class name with external code
		 */
		$wc1c_api_class_name = apply_filters('wc1c_api_loading_class_name', $default_class_name);

		/**
		 * New class not found? goto default
		 */
		if(!class_exists($wc1c_api_class_name))
		{
			$wc1c_api_class_name = $default_class_name;
		}

		/**
		 * Set api (run)
		 */
		$this->set_api(new $wc1c_api_class_name());
	}

	/**
	 * Loading tools
	 *
	 * @param string $tool_id
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function load_tools($tool_id = '')
	{
		$tools = [];

		try
		{
			$tool_example = new Wc1c_Tool_Example();

			$tool_example->set_id('example');
			$tool_example->set_version(WC1C_VERSION);
			$tool_example->set_name(__('Example tool', 'wc1c'));
			$tool_example->set_description(__('Демонстрационный инструмент, не несущий никакой функциональной нагрузки.', 'wc1c'));

			$tools[$tool_example->get_id()] = $tool_example;
		}
		catch(Exception $e)
		{
			//todo: exception
		}

		/**
		 * External tools loading is enable
		 */
		if('yes' === $this->settings()->get('extensions_tools'))
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
			//todo: exception
		}

		return true;
	}

	/**
	 * Extensions load
	 *
	 * @throws Exception
	 */
	public function load_extensions()
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
			throw new Exception('load_extensions: set_extensions - ' . $e->getMessage());
		}
	}

	/**
	 * Get schemas
	 *
	 * @param string $schema_id
	 *
	 * @return array|mixed
	 * @throws Exception
	 */
	public function get_schemas($schema_id = '')
	{
		if('' !== $schema_id)
		{
			if(array_key_exists($schema_id, $this->schemas))
			{
				return $this->schemas[$schema_id];
			}

			throw new Exception('get_schemas: $schema_id is unavailable');
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
	public function get_tools($tool_id = '')
	{
		if('' !== $tool_id)
		{
			if(array_key_exists($tool_id, $this->tools))
			{
				return $this->tools[$tool_id];
			}

			throw new Exception('get_tools: $schema_id is unavailable');
		}

		return $this->tools;
	}

	/**
	 * Set tools
	 *
	 * @param array $tools
	 *
	 * @throws Exception
	 * @return bool
	 */
	public function set_tools($tools)
	{
		if(is_array($tools))
		{
			$this->tools = $tools;

			return true;
		}

		throw new Exception('set_tools: $tools is not valid');
	}

	/**
	 * Get initialized extensions
	 *
	 * @param string $extension_id
	 *
	 * @return array|object
	 * @throws Exception
	 */
	public function get_extensions($extension_id = '')
	{
		if('' !== $extension_id)
		{
			if(array_key_exists($extension_id, $this->extensions))
			{
				return $this->extensions[$extension_id];
			}

			throw new Exception('get_extensions: $extension_id is unavailable');
		}

		return $this->extensions;
	}

	/**
	 * @param array $extensions
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function set_extensions($extensions)
	{
		if(is_array($extensions))
		{
			$this->extensions = $extensions;

			return true;
		}

		throw new Exception('set_extensions: $extensions is not valid');
	}
}