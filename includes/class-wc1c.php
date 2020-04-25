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
	 * Plugin environment
	 *
	 * @var null
	 */
	private $environment = null;

	/**
	 * Base settings
	 *
	 * @var array
	 */
	public $settings = array
	(
		'status' => 'off',
		'logger' => 'on',
	);

	/**
	 * All schemas
	 *
	 * @var array
	 */
	private $schemas = array();

	/**
	 * All tools
	 *
	 * @var array
	 */
	private $tools = array();

	/**
	 * All extensions
	 *
	 * @var array
	 */
	private $extensions = array();

	/**
	 * All configurations
	 *
	 * @var array
	 */
	public $configurations = array();

	/**
	 * Logger
	 *
	 * @var null|Wc1c_Logger
	 */
	public $logger = null;

	/**
	 * Plugin api
	 *
	 * @var null|Wc1c_Api
	 */
	private $api = null;

	/**
	 * Current configuration identifier
	 *
	 * @var bool|integer
	 */
	private $config_current_id = false;

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

		$this->init_includes();
		$this->init_hooks();

		// hook
		do_action('wc1c_after_loading');
	}

	/**
	 * Initializing actions and filters
	 */
	private function init_hooks()
	{
		// init
		add_action('init', array($this, 'init'), 0);

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
			$environ = $this->load_environment();

			if(false === $environ)
			{
				return false;
			}
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
			$this->logger()->alert('init: load settings error, ' . $e->getMessage());
			return false;
		}

		try
		{
			$this->reload_logger();
		}
		catch(Exception $e)
		{
			$this->logger()->alert('init: ' . $e->getMessage());
			return false;
		}

		$this->init_config_current_id();
		$this->load_textdomain();

		try
		{
			$this->load_extensions();
		}
		catch(Exception $e)
		{
			$this->logger()->alert('init: ' . $e->getMessage());
			return false;
		}

		$this->load_tools();
		$this->load_schemas();

		/**
		 * API
		 */
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
		$logger_level = $this->get_settings('logger', 400);
		$directory_name = $this->get_settings('upload_directory_name', 'wc1c');

		$logger_path = $this->environment()->get('upload_directory') . DIRECTORY_SEPARATOR . $directory_name;
		$logger_name = 'wc1c.main.log';

		$this->environment()->set('wc1c_upload_directory', $logger_path);

		if($logger_level && $directory_name)
		{
			$this->logger()->set_level($logger_level);
			$this->logger()->set_path($logger_path);
			$this->logger()->set_name($logger_name);
		}
		else
		{
			throw new Exception('reload_logger: error');
		}

		return true;
	}

	/**
	 * Include of files
	 */
	public function init_includes()
	{
		// hook
		do_action('wc1c_before_includes');

		/**
		 * Abstract
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-logger.php';
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-schema.php';
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-tool.php';

		/**
		 * Core
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-environment.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-logger.php';
		include_once WC1C_PLUGIN_PATH . 'includes/class-wc1c-configuration.php';

		/**
		 * Schemas
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/schemas/class-wc1c-schema-logger.php';
		include_once WC1C_PLUGIN_PATH . 'includes/schemas/default/class-wc1c-schema-default.php';

		/**
		 * Default tools
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/tools/class-wc1c-tool-default.php';

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
	 * Get environment
	 *
	 * @return null|Wc1c_Environment
	 */
	public function environment()
	{
		return $this->environment;
	}

	/**
	 * Set environment
	 *
	 * @param Wc1c_Environment $environment
	 */
	public function set_environment($environment)
	{
		$this->environment = $environment;
	}

	/**
	 * Loading environment
	 *
	 * @return bool
	 */
	public function load_environment()
	{
		try
		{
			$environment = new Wc1c_Environment();
		}
		catch(Exception $e)
		{
			return false;
		}

		$this->set_environment($environment);

		return true;
	}

	/**
	 * Localisation loading
	 */
	public function load_textdomain()
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

		/**
		 * Unload & load
		 */
		unload_textdomain('wc1c');
		load_textdomain('wc1c', WP_LANG_DIR . '/plugins/wc1c-' . $locale . '.mo');
		load_textdomain('wc1c', WC1C_PLUGIN_PATH . 'languages/wc1c-' . $locale . '.mo');

		$this->logger()->info('load_textdomain: success');
	}

	/**
	 * Get current config id
	 *
	 * @return bool|integer
	 */
	public function get_config_current_id()
	{
		return $this->config_current_id;
	}

	/**
	 * Set current config id
	 *
	 * @param $id
	 *
	 * @return $this
	 */
	public function set_config_current_id($id)
	{
		$this->config_current_id = $id;

		return $this;
	}

	/**
	 * Configuration current identifier initializing
	 *
	 * @return bool|integer
	 */
	public function init_config_current_id()
	{
		/**
		 * Default id
		 */
		$config_id = 0;

		/**
		 * Api requests
		 */
		if(is_wc1c_api_request() && isset($_GET['config_id']))
		{
			$config_id = (int)$_GET['config_id'];
		}

		/**
		 * Admin pages
		 */
		if(is_wc1c_admin_request() && isset($_GET['config_id']))
		{
			$config_id = (int)$_GET['config_id'];
		}

		/**
		 * Final
		 */
		if(0 < $config_id && 99999999 > $config_id)
		{
			$this->set_config_current_id($config_id);

			$this->logger()->info('init_config_current_id: WC1C init_config_current_id: ' . $config_id);
		}

		return $this->get_config_current_id();
	}

	/**
	 * Plugin settings loading
	 *
	 * @throws Exception
	 */
	public function load_settings()
	{
		/**
		 * Get settings from DB
		 */
		$settings = get_site_option('wc1c', array());

		/**
		 * Loading with external code
		 */
		$settings = apply_filters('wc1c_settings_loading', $settings);

		/**
		 * Exception
		 */
		if(!is_array($settings))
		{
			throw new Exception('$settings is not array');
		}

		/**
		 * Final set
		 */
		$this->set_settings
		(
			array_merge
			(
				$this->get_settings(),
				$settings
			)
		);
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
		/**
		 * Not all
		 */
		if($type !== 'all')
		{
			/**
			 * Current
			 */
			if($type == 'current' && $this->get_config_current_id() !== false && array_key_exists($this->get_config_current_id(), $this->configurations))
			{
				return $this->configurations[$this->get_config_current_id()];
			}

			/**
			 * By id
			 */
			if(is_numeric($type) && array_key_exists($type, $this->configurations))
			{
				return $this->configurations[$type];
			}

			/**
			 * Error
			 */
			return false;
		}

		/**
		 * All
		 */
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
	 * Initializing schemas
	 *
	 * If a schema ID is specified, only the specified schema is loaded
	 *
	 * @param string $schema_id
	 *
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
			return false;
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
				return false;
			}

			/**
			 * Schema initialized
			 */
			if(is_object($schemas[$schema_id]['instance']))
			{
				return true;
			}

			/**
			 * Schema valid
			 */
			if(array_key_exists('class', $schemas[$schema_id]) && class_exists($schemas[$schema_id]['class']))
			{
				/**
				 * Create schema object & save to buffer
				 */
				$schemas[$schema_id]['instance'] = new $schemas[$schema_id]['class'](); // todo: exceptions
				$schemas[$schema_id]['instance']->set_schema_prefix('wc1c_schema_' . $schema_id);
				$schemas[$schema_id]['instance']->set_id($schema_id);

				/**
				 * Set options
				 */
				if($this->get_config_current_id())
				{
					$options = $this->get_configurations('current');

					$schemas[$schema_id]['instance']->set_options($options['instance']->get_options());
					$schemas[$schema_id]['instance']->set_configuration_prefix('wc1c_configuration_' . $this->get_config_current_id());
				}

				/**
				 * Init
				 */
				if(method_exists($schemas[$schema_id]['instance'], 'init'))
				{
					if($schemas[$schema_id]['instance']->get_schema_prefix() !== '' && $schemas[$schema_id]['instance']->get_configuration_prefix() !== '')
					{
						$schemas[$schema_id]['instance']->set_prefix
						(
							'wc1c_prefix_' . $schema_id . '_' . $this->get_config_current_id()
						);
					}
					$schemas[$schema_id]['instance']->init();
				}

				/**
				 * Reload buffer
				 */
				$this->set_schemas($schemas);

				/**
				 * Success
				 */
				return true;
			}

			/**
			 * Error
			 */
			return false;
		}

		/**
		 * Init all schemas
		 */
		$return = true;
		foreach($schemas as $schema_id => $schema_data)
		{
			$result = $this->init_schemas($schema_id);
			if($result === false)
			{
				$return = false;
			}
		}
		return $return;
	}

	/**
	 * Schemas loading
	 *
	 * @return array - loaded schemas
	 */
	public function load_schemas()
	{
		/**
		 * Default schema
		 */
		$schemas['default'] = array
		(
			'name' => __('Default schema', 'wc1c'),
			'description' => __('Basic schema for exchanging data on products via CommerceML of different versions.', 'wc1c'),
			'author_name' => 'Mofsy',
			'version' => '1.0.0',
			'version_min' => '1.0.0',
			'version_max' => '1.0.0',
			'version_php_min' => '5.3.0',
			'version_php_max' => '7.4.0',
			'class' => 'Wc1c_Schema_Default',
			'instance' => null
		);

		/**
		 * External schemas loading
		 */
		if('yes' == $this->get_settings('external_schemas'))
		{
			$schemas = apply_filters('wc1c_schemas_loading', $schemas);
		}

		$this->logger()->debug('WC1C load_schemas', $schemas);

		/**
		 * Final setup
		 */
		$this->set_schemas($schemas);

		/**
		 * Return loaded schemas
		 */
		return $this->get_schemas();
	}

	/**
	 * Set schemas
	 *
	 * @param $schemas
	 *
	 * @return $this
	 */
	public function set_schemas($schemas)
	{
		$this->schemas = $schemas;

		return $this;
	}

	/**
	 * Save plugin settings
	 *
	 * @param $settings array
	 *
	 * @return bool
	 */
	public function save_settings($settings)
	{
		/**
		 * Apply filters
		 */
		$settings = apply_filters('wc1c_settings_save', $settings);

		/**
		 * Reload buffer
		 */
		$this->set_settings($settings);

		/**
		 * Update in DB
		 *
		 * Required WP 4.2.0 autoload option
		 */
		return update_option('wc1c', $settings, 'no');
	}

	/**
	 * Get plugin settings
	 *
	 * @param string $key - optional
	 * @param null $default
	 *
	 * @return array|bool|mixed
	 */
	public function get_settings($key = '', $default = null)
	{
		if($key !== '')
		{
			if(is_array($this->settings) && array_key_exists($key, $this->settings))
			{
				return $this->settings[$key];
			}

			if(false === is_null($default))
			{
				return $default;
			}

			return false;
		}

		return $this->settings;
	}

	/**
	 * Set plugin settings
	 *
	 * @param $settings
	 */
	public function set_settings($settings)
	{
		$this->settings = $settings;
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
		$configurations = array();

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
	 * @param array $configurations
	 */
	public function set_configurations($configurations)
	{
		$this->configurations = $configurations;
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
	 */
	public function set_logger($logger)
	{
		$this->logger = $logger;

		return $this;
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
	 * @return null
	 */
	public function get_api()
	{
		return $this->api;
	}

	/**
	 * Set API
	 *
	 * @param null $api
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
	 * @return array
	 */
	public function load_tools($tool_id = '')
	{
		/**
		 * Default tool
		 */
		$tools['default'] = array
		(
			'name' => __('Default tool', 'wc1c'),
			'description' => '',
			'author_name' => 'Mofsy',
			'version' => '1.0.0',
			'wc1c_version_min' => '1.0.0',
			'wc1c_version_max' => '1.0.0',
			'version_php_min' => '5.3.0',
			'version_php_max' => '7.4.0',
			'class' => 'Wc1c_Tool_Default',
			'instance' => null
		);

		/**
		 * External tools loading is enable
		 */
		if('yes' === $this->get_settings('external_tools'))
		{
			$tools = apply_filters('wc1c_tools_loading', $tools);
		}

		$this->logger()->debug('WC1C load_tools', $tools);

		/**
		 * Final setup
		 */
		$this->set_tools($tools);

		/**
		 * Return loaded tools
		 */
		return $this->get_tools();
	}

	/**
	 * Extensions load
	 *
	 * @throws Exception
	 *
	 * @return array
	 */
	public function load_extensions()
	{
		$extensions = array();

		if('yes' === $this->get_settings('enable_extensions', 'yes'))
		{
			$extensions = apply_filters('wc1c_extensions_loading', $extensions);
		}

		if(!is_array($extensions))
		{
			throw new Exception('load_extensions: $extensions is not array');
		}

		$this->logger()->debug('load_extensions: $extensions', $extensions);

		$this->set_extensions($extensions);

		return $this->get_extensions();
	}

	/**
	 * Get schemas
	 *
	 * @return array
	 */
	public function get_schemas()
	{
		return $this->schemas;
	}

	/**
	 * Get tools
	 *
	 * @return array
	 */
	public function get_tools()
	{
		return $this->tools;
	}

	/**
	 * Set tools
	 *
	 * @param array $tools
	 */
	public function set_tools($tools)
	{
		$this->tools = $tools;
	}

	/**
	 * @return array
	 */
	public function get_extensions()
	{
		return $this->extensions;
	}

	/**
	 * @param array $extensions
	 */
	public function set_extensions($extensions)
	{
		$this->extensions = $extensions;
	}
}