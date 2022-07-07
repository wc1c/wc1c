<?php namespace Wc1c;

defined('ABSPATH') || exit;

use wpdb;
use Psr\Log\LoggerInterface;
use Wc1c\Exceptions\Exception;
use Wc1c\Log\Formatter;
use Wc1c\Log\Handler;
use Wc1c\Log\Logger;
use Wc1c\Log\Processor;
use Wc1c\Settings\ConnectionSettings;
use Wc1c\Settings\Contracts\SettingsContract;
use Wc1c\Settings\InterfaceSettings;
use Wc1c\Settings\LogsSettings;
use Wc1c\Settings\MainSettings;
use Wc1c\Traits\SingletonTrait;

/**
 * Core
 *
 * @package Wc1c
 */
final class Core
{
	use SingletonTrait;

	/**
	 * @var Loader
	 */
	private $loader;

	/**
	 * @var array
	 */
	private $log = [];

	/**
	 * @var Context
	 */
	private $context;

	/**
	 * @var Timer
	 */
	private $timer;

	/**
	 * @var SettingsContract
	 */
	private $settings = [];

	/**
	 * @var Receiver
	 */
	private $receiver;

	/**
	 * @var Tecodes\Client
	 */
	private $tecodes;

	/**
	 * Core constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		do_action('wc1c_core_loaded');
	}

	/**
	 * @param $context
	 * @param $loader
	 *
	 * @return void
	 */
	public function register($context, $loader)
	{
		if(has_filter('wc1c_context_loading'))
		{
			$context = apply_filters('wc1c_context_loading', $context);
		}

		$this->context = $context;

		if(has_filter('wc1c_loader_loading'))
		{
			$loader = apply_filters('wc1c_loader_loading', $loader);
		}

		$this->loader = $loader;

		// init
		add_action('init', [$this, 'init'], 3);

		// admin
		if(false !== is_admin())
		{
			add_action('init', [$this, 'admin'], 5);
		}
	}

	/**
	 * Initialization
	 */
	public function init()
	{
		// hook
		do_action('wc1c_before_init');

		$this->localization();

		try
		{
			$this->timer();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert(__('Timer not loaded.', 'wc1c'), ['exception' => $e]);
			return;
		}

		try
		{
			$this->extensions()->load();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert(__('Extensions not loaded.', 'wc1c'), ['exception' => $e]);
		}

		try
		{
			$this->extensions()->init();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert(__('Extensions not initialized.', 'wc1c'), ['exception' => $e]);
		}

		try
		{
			$this->schemas()->load();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert(__('Schemas not loaded.', 'wc1c'), ['exception' => $e]);
		}

		try
		{
			$this->tools()->load();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert(__('Tools not loaded.', 'wc1c'), ['exception' => $e]);
		}

		if(false !== wc1c()->context()->isReceiver() || false !== wc1c()->context()->isAdmin())
		{
			try
			{
				$this->tools()->init();
			}
			catch(Exception $e)
			{
				wc1c()->log()->alert(__('Tools not initialized.', 'wc1c'), ['exception' => $e]);
			}
		}

		if(false !== wc1c()->context()->isReceiver())
		{
			try
			{
				$this->loadReceiver();
			}
			catch(Exception $e)
			{
				wc1c()->log()->alert(__('Receiver not loaded.', 'wc1c'), ['exception' => $e]);
			}
		}

		// hook
		do_action('wc1c_after_init');
	}

	/**
	 * Extensions
	 *
	 * @return Extensions\Core
	 */
	public function extensions()
	{
		return Extensions\Core::instance();
	}

	/**
	 * Filesystem
	 *
	 * @return Filesystem
	 */
	public function filesystem()
	{
		return Filesystem::instance();
	}

	/**
	 * Schemas
	 *
	 * @return Schemas\Core
	 */
	public function schemas()
	{
		return Schemas\Core::instance();
	}

	/**
	 * Environment
	 *
	 * @return Environment
	 */
	public function environment()
	{
		return Environment::instance();
	}

	/**
	 * Views
	 *
	 * @return Views
	 */
	public function views()
	{
		return Views::instance();
	}

	/**
	 * Context
	 *
	 * @return Context
	 */
	public function context()
	{
		return $this->context;
	}

	/**
	 * Loader
	 *
	 * @return Loader
	 */
	public function loader()
	{
		return $this->loader;
	}

	/**
	 * Tools
	 *
	 * @return Tools\Core
	 */
	public function tools()
	{
		return Tools\Core::instance();
	}

	/**
	 * Logger
	 *
	 * @param string $channel
	 * @param string $name
	 * @param mixed $hard_level
	 *
	 * @return LoggerInterface
	 */
	public function log($channel = 'main', $name = '', $hard_level = null)
	{
		$channel = strtolower($channel);

		if(!isset($this->log[$channel]))
		{
			if('' === $name)
			{
				$name = $channel;
			}

			$path = '';
			$max_files = $this->settings('logs')->get('logger_files_max', 30);

			$logger = new Logger($channel);

			switch($channel)
			{
				case 'receiver':
					$level = $this->settings('logs')->get('logger_receiver_level', 'logger_level');
					break;
				case 'tools':
					$path = $this->environment()->get('wc1c_tools_logs_directory') . '/' . $name . '.log';
					$level = $this->settings('logs')->get('logger_tools_level', 'logger_level');
					break;
				case 'schemas':
					$path = $this->environment()->get('wc1c_schemas_logs_directory') . '/' . $name . '.log';
					$level = $this->settings('logs')->get('logger_schemas_level', 'logger_level');
					break;
				case 'configurations':
					$path = $name . '.log';
					$level = $this->settings('logs')->get('logger_configurations_level', 'logger_level');
					break;
				default:
					$level = $this->settings('logs')->get('logger_level', 300);
			}

			if('logger_level' === $level)
			{
				$level = $this->settings('logs')->get('logger_level', 300);
			}

			if(!is_null($hard_level))
			{
				$level = $hard_level;
			}

			if('' === $path)
			{
				$path = $this->environment()->get('wc1c_logs_directory') . '/main.log';
			}

			try
			{
				$uid_processor = new Processor();
				$formatter = new Formatter();
				$handler = new Handler($path, $max_files, $level);

				$handler->setFormatter($formatter);

				$logger->pushProcessor($uid_processor);
				$logger->pushHandler($handler);
			}
			catch(\Exception $e){}

			/**
			 * Внешние назначения для логгера
			 *
			 * @param LoggerInterface $logger Текущий логгер
			 *
			 * @return LoggerInterface
			 */
			if(has_filter('wc1c_log_load_before'))
			{
				$logger = apply_filters('wc1c_log_load_before', $logger);
			}

			$this->log[$channel] = $logger;
		}

		return $this->log[$channel];
	}

	/**
	 * Settings
	 *
	 * @param string $context
	 *
	 * @return SettingsContract
	 */
	public function settings($context = 'main')
	{
		if(!isset($this->settings[$context]))
		{
			switch($context)
			{
				case 'connection':
					$class = ConnectionSettings::class;
					break;
				case 'logs':
					$class = LogsSettings::class;
					break;
				case 'interface':
					$class = InterfaceSettings::class;
					break;
				default:
					$class = MainSettings::class;
			}

			$settings = new $class();

			try
			{
				$settings->init();
			}
			catch(Exception $e)
			{
				wc1c()->log()->error($e->getMessage(), ['exception' => $e]);
			}

			$this->settings[$context] = $settings;
		}

		return $this->settings[$context];
	}

	/**
	 * Timer
	 *
	 * @return Timer
	 */
	public function timer()
	{
		if(is_null($this->timer))
		{
			$timer = new Timer();

			$php_max_execution = $this->environment()->get('php_max_execution_time', 20);

			if($php_max_execution !== $this->settings()->get('php_max_execution_time', $php_max_execution))
			{
				$php_max_execution = $this->settings()->get('php_max_execution_time', $php_max_execution);
			}

			$timer->setMaximum($php_max_execution);

			$this->timer = $timer;
		}

		return $this->timer;
	}

	/**
	 * Tecodes
	 *
	 * @return Tecodes\Client
	 */
	public function tecodes()
	{
		if($this->tecodes instanceof Tecodes\Client)
		{
			return $this->tecodes;
		}

		if(!class_exists('Tecodes_Local'))
		{
			include_once $this->environment()->get('plugin_directory_path') . '/vendor/tecodes/tecodes-local/bootstrap.php';
		}

		$options =
		[
			'timeout' => 5,
			'verify_ssl' => false,
			'version' => 'tecodes/v1'
		];

		$tecodes_local = new Tecodes\Client('https://wc1c.info/', $options);

		/**
		 * Languages
		 */
		$tecodes_local->status_messages =
		[
			'status_1' => __('This activation code is active.', 'wc1c'),
			'status_2' => __('Error: This activation code has expired.', 'wc1c'),
			'status_3' => __('Activation code republished. Awaiting reactivation.', 'wc1c'),
			'status_4' => __('Error: This activation code has been suspended.', 'wc1c'),
			'code_not_found' => __('This activation code is not found.', 'wc1c'),
			'localhost' => __('This activation code is active (localhost).', 'wc1c'),
			'pending' => __('Error: This activation code is pending review.', 'wc1c'),
			'download_access_expired' => __('Error: This version of the software was released after your download access expired. Please downgrade software or contact support for more information.', 'wc1c'),
			'missing_activation_key' => __('Error: The activation code variable is empty.', 'wc1c'),
			'could_not_obtain_local_code' => __('Error: I could not obtain a new local code.', 'wc1c'),
			'maximum_delay_period_expired' => __('Error: The maximum local code delay period has expired.', 'wc1c'),
			'local_code_tampering' => __('Error: The local key has been tampered with or is invalid.', 'wc1c'),
			'local_code_invalid_for_location' => __('Error: The local code is invalid for this location.', 'wc1c'),
			'missing_license_file' => __('Error: Please create the following file (and directories if they dont exist already): ', 'wc1c'),
			'license_file_not_writable' => __('Error: Please make the following path writable: ', 'wc1c'),
			'invalid_local_key_storage' => __('Error: I could not determine the local key storage on clear.', 'wc1c'),
			'could_not_save_local_key' => __('Error: I could not save the local key.', 'wc1c'),
			'code_string_mismatch' => __('Error: The local code is invalid for this activation code.', 'wc1c'),
			'code_status_delete' => __('Error: This activation code has been deleted.', 'wc1c'),
			'code_status_draft' => __('Error: This activation code has draft.', 'wc1c'),
			'code_status_available' => __('Error: This activation code has available.', 'wc1c'),
			'code_status_blocked' => __('Error: This activation code has been blocked.', 'wc1c'),
		];

		$tecodes_local->set_local_code_storage(new Tecodes\Storage());
		$tecodes_local->set_instance(new Tecodes\Instance());

		$tecodes_local->validate();

		$this->tecodes = $tecodes_local;

		return $this->tecodes;
	}

	/**
	 * Get Receiver
	 *
	 * @return Receiver
	 */
	public function receiver()
	{
		return $this->receiver;
	}

	/**
	 * Set Receiver
	 *
	 * @param Receiver $receiver
	 */
	public function setReceiver($receiver)
	{
		$this->receiver = $receiver;
	}

	/**
	 * Receiver loading
	 *
	 * @return void
	 * @throws Exception
	 */
	private function loadReceiver()
	{
		$default_class_name = Receiver::class;

		$use_class_name = apply_filters('wc1c_receiver_loading_class_name', $default_class_name);

		if(false === class_exists($use_class_name))
		{
			wc1c()->log()->error(__('Receiver loading: class is not exists, use is default.', 'wc1c'), ['context' => $use_class_name]);
			$use_class_name = $default_class_name;
		}

		try
		{
			$receiver = new $use_class_name();
		}
		catch(Exception $e)
		{
			throw $e;
		}

		$receiver->register();

		try
		{
			$this->setReceiver($receiver);
		}
		catch(Exception $e)
		{
			throw $e;
		}
	}

	/**
	 * Load localisation
	 */
	public function localization()
	{
		/** WP 5.x or later */
		if(function_exists('determine_locale'))
		{
			$locale = determine_locale();
		}
		else
		{
			$locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
		}

		if(has_filter('plugin_locale'))
		{
			$locale = apply_filters('plugin_locale', $locale, 'wc1c');
		}

		unload_textdomain('wc1c');

		load_textdomain('wc1c', WP_LANG_DIR . '/plugins/wc1c-' . $locale . '.mo');
		load_textdomain('wc1c', wc1c()->environment()->get('plugin_directory_path') . 'assets/languages/wc1c-' . $locale . '.mo');

		wc1c()->log()->debug(__('Localization loaded.', 'wc1c'), ['locale' => $locale]);
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

	/**
	 * Main instance of Admin
	 *
	 * @return Admin
	 */
	public function admin()
	{
		return Admin::instance();
	}

	/**
	 * Get data if set, otherwise return a default value or null
	 * Prevents notices when data is not set
	 *
	 * @param mixed $var variable
	 * @param string $default default value
	 *
	 * @return mixed
	 */
	public function getVar(&$var, $default = null)
	{
		return isset($var) ? $var : $default;
	}

	/**
	 * Define constant if not already set
	 *
	 * @param string $name constant name
	 * @param string|bool $value constant value
	 */
	public function define($name, $value)
	{
		if(!defined($name))
		{
			define($name, $value);
		}
	}
}