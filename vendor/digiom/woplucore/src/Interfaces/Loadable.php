<?php namespace Woplucore\Interfaces;

use Exception;

/**
 * Interface Loadable
 *
 * @package Woplucore\Interfaces
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
	public function addNamespace(string $namespace, string $directory, bool $prepend = false): void;

	/**
	 * Register loader with SPL autoloader stack.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function register(): void;
}