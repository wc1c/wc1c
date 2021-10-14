<?php
/**
 * Plugin Name: WC1C
 * Plugin URI: https://wc1c.info
 * Description: Implementation of a mechanism for flexible exchange of various data between 1C products and a site running WordPress using the WooCommerce plugin.
 * Version: 0.6.0
 * WC requires at least: 3.2
 * WC tested up to: 5.8
 * Requires at least: 4.2
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
	define('WC1C_PLUGIN_FILE', __FILE__);
	define('WC1C_PLUGIN_PATH', plugin_dir_path(WC1C_PLUGIN_FILE));

	include_once __DIR__ . '/includes/functions-wc1c.php';
	include_once __DIR__ . '/includes/class-wc1c-autoloader.php';

	register_activation_hook(WC1C_PLUGIN_FILE, 'wc1c_activation');
	register_deactivation_hook(WC1C_PLUGIN_FILE, 'wc1c_deactivation');

	add_action('plugins_loaded', 'WC1C', 10);
}