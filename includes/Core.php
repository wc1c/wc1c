<?php namespace Wc1c;

defined('ABSPATH') || exit;

use wpdb;
use Wc1c\Log\Formatter;
use Wc1c\Log\Handler;
use Psr\Log\LoggerInterface;
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Interfaces\SettingsInterface;
use Wc1c\Log\Logger;
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
	 * @var SettingsInterface
	 */
	private $settings;

	/**
	 * @var Receiver
	 */
	private $receiver;

	/**
	 * Core constructor.
	 *
	 * @return void
	 */
	public function __construct()
	{
		do_action(WC1C_PREFIX . 'loading');
	}

	/**
	 * @param $context
	 *
	 * @return void
	 */
	public function register($context)
	{
		$this->context = apply_filters(WC1C_PREFIX . 'context_loading', $context);

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
		do_action(WC1C_PREFIX . 'before_init');

		$this->localization();

		try
		{
			$this->timer();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('Timer exception - ' . $e->getMessage());
		}

		try
		{
			$this->extensions();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('Extensions exception - ' . $e->getMessage());
		}

		try
		{
			$this->schemas();
		}
		catch(Exception $e)
		{
			wc1c()->log()->alert('Schemas exception - ' . $e->getMessage());
		}

		if(false !== wc1c()->context()->isReceiver() || false !== wc1c()->context()->isWc1cAdmin())
		{
			try
			{
				$this->tools();
			}
			catch(Exception $e)
			{
				wc1c()->log()->alert('Tools exception - ' . $e->getMessage());
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
				wc1c()->log()->alert('Receiver exception - ' . $e->getMessage());
			}
		}

		// hook
		do_action(WC1C_PREFIX . 'after_init');
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
	 * Templates
	 *
	 * @return Templates
	 */
	public function templates()
	{
		return Templates::instance();
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
	 * @return LoggerInterface
	 */
	public function log($channel = 'main')
	{
		$channel = strtolower($channel);

		if(!isset($this->log[$channel]))
		{
			$logger = new Logger($channel);

			$path = $this->environment()->get('wc1c_logs_directory') . '/' . $channel .'.log';
			$level = $this->settings()->get('logger_level', '');

			if($level !== '')
			{
				try
				{
					$formatter = new Formatter();
					$handler = new Handler($path, $level);
					$handler->setFormatter($formatter);

					$logger->pushHandler($handler);
				}
				catch(\Exception $e){}
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

		$use_class_name = apply_filters(WC1C_PREFIX . 'receiver_loading_class_name', $default_class_name);

		if(false === class_exists($use_class_name))
		{
			$this->log()->info(WC1C_PREFIX . 'receiver_loading_class_name: class is not exists - ' . $use_class_name);
			$use_class_name = $default_class_name;
		}

		try
		{
			$api = new $use_class_name();
		}
		catch(Exception $e)
		{
			throw new Exception('Receiver is not loaded');
		}

		try
		{
			$this->setReceiver($api);
		}
		catch(Exception $e)
		{
			throw new Exception('Set receiver - ' . $e->getMessage());
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