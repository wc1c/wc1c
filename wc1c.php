<?php
/**
 * Plugin Name: WC1C
 * Plugin URI: https://wc1c.info
 * Description: Implementation of a mechanism for flexible exchange of various data between 1C products and a site running WordPress using the WooCommerce plugin.
 * Version: 0.7.0
 * WC requires at least: 3.5
 * WC tested up to: 6.0
 * Requires at least: 4.7
 * Requires PHP: 5.6
 * Text Domain: wc1c
 * Domain Path: /languages
 * Copyright: WC1C team © 2018-2022
 * Author: WC1C team
 * Author URI: https://wc1c.info
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Wc1c
 **/
defined('ABSPATH') || exit;

if(version_compare(PHP_VERSION, '5.6.0') < 0)
{
	return false;
}

if(false === defined('WC1C_PLUGIN_FILE'))
{
	/**
	 * Main instance of Wc1c
	 *
	 * @return Wc1c\Core
	 */
	function wc1c()
	{
		return Wc1c\Core::instance();
	}

	define('WC1C_PREFIX', 'wc1c_');
	define('WC1C_ADMIN_PREFIX', 'wc1c_admin_');

	define('WC1C_PLUGIN_FILE', __FILE__);
	define('WC1C_PLUGIN_PATH', plugin_dir_path(WC1C_PLUGIN_FILE));
	define('WC1C_PLUGIN_URL', plugin_dir_url(__FILE__));
	define('WC1C_PLUGIN_NAME', plugin_basename(WC1C_PLUGIN_FILE));

	include_once __DIR__ . '/includes/Loader.php';

	$loader = new Wc1c\Loader();

	try
	{
		$loader->register();
	}
	catch(Exception $e){}
}