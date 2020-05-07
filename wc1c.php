<?php
/**
 * Plugin Name: WC1C: integration of WooCommerce with 1C products (different configurations)
 * Plugin URI: https://wc1c.info
 * Description: Implementation of a mechanism for flexible exchange of various data between 1C products and a site running WordPress using the WooCommerce plugin.
 * Version: 0.5.0
 * WC requires at least: 3.0
 * WC tested up to: 4.0
 * Text Domain: wc1c
 * Domain Path: /languages
 * Copyright: WC1C team © 2018-2020
 * Author: WC1C team
 * Author URI: https://wc1c.info
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package Wc1c
 **/
defined('ABSPATH') || exit;

if(!defined('WC1C_VERSION'))
{
	define('WC1C_PLUGIN_FILE', __FILE__);

	$plugin_data = get_file_data(WC1C_PLUGIN_FILE, array('Version' => 'Version'));
	define('WC1C_VERSION', $plugin_data['Version']);
	// todo: move to environment
	define('WC1C_PLUGIN_URL', plugin_dir_url(WC1C_PLUGIN_FILE));
	define('WC1C_PLUGIN_NAME', plugin_basename(WC1C_PLUGIN_FILE));
	define('WC1C_PLUGIN_PATH', plugin_dir_path(WC1C_PLUGIN_FILE));

	include_once __DIR__ . '/includes/class-wc1c.php';
	include_once __DIR__ . '/includes/functions-wc1c.php';
}

register_activation_hook(__FILE__, 'wc1c_install');

add_action('plugins_loaded', 'WC1C', 0);