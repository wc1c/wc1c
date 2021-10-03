<?php
/**
 * Autoloader class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Autoloader
{
	/**
	 * Path to the includes directory.
	 *
	 * @var string
	 */
	private $include_path;

	/**
	 * Wc1c_Autoloader constructor.
	 */
	public function __construct()
	{
		if(function_exists('__autoload'))
		{
			spl_autoload_register( '__autoload');
		}

		spl_autoload_register([$this, 'run']);

		$this->include_path = untrailingslashit(plugin_dir_path(WC1C_PLUGIN_FILE)) . '/';
	}

	/**
	 * Take a class name and turn it into a file name.
	 *
	 * @param string $class Class name.
	 *
	 * @return string
	 */
	private function get_file_name_from_class($class)
	{
		if(0 === strpos($class, 'abstract'))
		{
			$class = str_replace( 'abstract', '', $class );
			return 'abstract-class' . str_replace( '_', '-', $class ) . '.php';
		}

		if(0 === strpos($class, 'trait') || 0 === strpos($class, 'interface'))
		{
			return str_replace( '_', '-', $class ) . '.php';
		}

		return 'class-' . str_replace( '_', '-', $class ) . '.php';
	}

	/**
	 * Include a class file.
	 *
	 * @param string $path File path.
	 * @return bool Successful or not.
	 */
	private function load_file($path)
	{
		if($path && is_readable($path))
		{
			include_once $path;
			return true;
		}
		return false;
	}

	/**
	 * Auto-load WC classes on demand to reduce memory consumption.
	 *
	 * @param string $class Class name.
	 */
	public function run($class)
	{
		$class = strtolower($class);

		$path = $this->include_path . 'includes/';

		$file = $this->get_file_name_from_class($class);

		if(0 === strpos($class, 'wc1c_schema'))
		{
			$path = $this->include_path . 'schemas/' . substr(str_replace('_', '-', $class), 12) . '/';
		}
		elseif(0 === strpos($class, 'wc1c_schema_'))
		{
			$path = $this->include_path . 'schemas/';
		}
		elseif(0 === strpos($class, 'wc1c_tool'))
		{
			$path = $this->include_path . 'tools/' . substr(str_replace('_', '-', $class), 10) . '/';
		}
		elseif(0 === strpos($class, 'wc1c_tool_'))
		{
			$path = $this->include_path . 'tools/';
		}
		elseif(0 === strpos($class, 'abstract_'))
		{
			$path = $this->include_path . 'includes/abstracts/';
		}
		elseif(0 === strpos($class, 'trait_'))
		{
			$path = $this->include_path . 'includes/traits/';
		}
		elseif(0 === strpos($class, 'interface_'))
		{
			$path = $this->include_path . 'includes/interfaces/';
		}
		elseif(0 === strpos($class, 'wc1c_admin_'))
		{
			$path = $this->include_path . 'includes/admin/';
		}

		if(empty($path) || !$this->load_file($path . $file))
		{
			$this->load_file($this->include_path . 'includes/' . $file);
		}
	}
}

new Wc1c_Autoloader();