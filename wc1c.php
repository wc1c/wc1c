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
 * Copyright: WC1C team © 2018-2021
 * Author: WC1C team
 * Author URI: https://wc1c.info
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Wc1c
 **/
defined('ABSPATH') || exit;

if(false === defined('WC1C_PLUGIN_FILE'))
{
	/**
	 * Main instance of Wc1c
	 *
	 * @return Wc1c\Core|boolean
	 */
	function wc1c()
	{
		if(version_compare(PHP_VERSION, '5.6.0') < 0)
		{
			return false;
		}

		if(!is_callable('Wc1c\Core::instance'))
		{
			return false;
		}

		if(!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')), true))
		{
			return false;
		}

		return Wc1c\Core::instance();
	}

	define('WC1C_PREFIX', 'wc1c_');
	define('WC1C_ADMIN_PREFIX', 'wc1c_admin_');

	define('WC1C_PLUGIN_FILE', __FILE__);
	define('WC1C_PLUGIN_PATH', plugin_dir_path(WC1C_PLUGIN_FILE));
	define('WC1C_PLUGIN_URL', plugin_dir_url(__FILE__));
	define('WC1C_PLUGIN_NAME', plugin_basename(WC1C_PLUGIN_FILE));

	include_once __DIR__ . '/includes/functions.php';
	include_once __DIR__ . '/includes/Autoloader.php';

	$loader = new Wc1c\Autoloader();

	$loader->addNamespace('Wc1c', __DIR__ . '/includes');
	$loader->addNamespace('Digiom\WordPress\Notices', __DIR__ . '/vendor/digiom/notices-wp/src');
	$loader->addNamespace('Psr\Log', __DIR__ . '/vendor/psr/Log');
	$loader->addNamespace('Monolog', __DIR__ . '/vendor/monolog/src/Monolog');

	$loader->register();

	add_action('plugins_loaded', 'wc1c', 10);
}