<?php namespace Digiom\Woplucore\Interfaces;

/**
 * Interface Loadable
 *
 * @package Digiom\Woplucore\Interfaces
 */
interface Loadable
{
	/**
	 * Adds a base directory for a namespace prefix.
	 *
	 * @param string $namespace The namespace prefix.
	 * @param string $directory A base directory for class files in the namespace.
	 * @param bool $prepend If true, prepend the base directory to the stack instead of appending it; this causes it to be searched first rather than last.
	 *
	 * @return void
	 */
	public function addNamespace(string $namespace, string $directory, bool $prepend = false);

	/**
	 * Register loader with SPL autoloader stack.
	 *
	 * @param string $file
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function register(string $file);

	/**
	 * Register loader with activation stack.
	 *
	 * @param callable $class
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function registerActivation($class);

	/**
	 * Register loader with deactivation stack.
	 *
	 * @param callable $class
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function registerDeactivation($class);

	/**
	 * Register loader with uninstall stack.
	 *
	 * @param callable $class
	 *
	 * @return void
	 * @throws \Exception
	 */
	public function registerUninstall($class);
}