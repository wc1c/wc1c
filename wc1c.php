<?php
/**
 * Plugin Name: WC1C
 * Plugin URI: https://wc1c.info
 * Description: Implementation of a mechanism for flexible exchange of various data between 1C products and a site running WordPress using the WooCommerce plugin.
 * Version: 0.13.0
 * WC requires at least: 4.3
 * WC tested up to: 7.2
 * Requires at least: 5.2
 * Requires PHP: 7.0
 * Text Domain: wc1c
 * Domain Path: /assets/languages
 * Copyright: WC1C team © 2018-2022
 * Author: WC1C team
 * Author URI: https://wc1c.info
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Wc1c
 **/
defined('ABSPATH') || exit;

if(version_compare(PHP_VERSION, '7.0') < 0)
{
	return false;
}

if(false === defined('WC1C_PLUGIN_FILE'))
{
	define('WC1C_PLUGIN_FILE', __FILE__);

	/**
	 * Main instance of WC1C
	 *
	 * @return Wc1c\Core
	 */
	function wc1c(): Wc1c\Core
	{
		return Wc1c\Core::instance();
	}

	include_once __DIR__ . '/vendor/autoload.php';

	$loader = new \Digiom\Woplucore\Loader();

	$loader->addNamespace('Wc1c', plugin_dir_path(WC1C_PLUGIN_FILE) . 'src');

	try
	{
		$loader->register(WC1C_PLUGIN_FILE);
	}
	catch(\Exception $e)
	{
		trigger_error($e->getMessage());
	}

	$loader->registerActivation([Wc1c\Activation::class, 'instance']);
	$loader->registerDeactivation([Wc1c\Deactivation::class, 'instance']);
	$loader->registerUninstall([Wc1c\Uninstall::class, 'instance']);

	wc1c()->register(new Wc1c\Context(), $loader);
}