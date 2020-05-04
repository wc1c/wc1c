<?php
/**
 * Main instance of Wc1c
 *
 * @return Wc1c|boolean
 */
function WC1C()
{
	if(is_callable('Wc1c::instance'))
	{
		return Wc1c::instance();
	}

	return false;
}

/**
 * Main instance of Wc1c_Admin
 *
 * @return Wc1c_Admin|boolean
 */
function WC1C_Admin()
{
	if(is_callable('Wc1c_Admin::instance'))
	{
		return Wc1c_Admin::instance();
	}

	return false;
}

/**
 * Use in plugin for DB queries
 *
 * @return wpdb
 */
function WC1C_Db()
{
	global $wpdb;
	return $wpdb;
}

/**
 * Install db tables
 *
 * @return bool
 */
function wc1c_install()
{
	$wc1c_db_version = 1;
	$current_db = get_site_option('wc1c_db_version');

	if($current_db == $wc1c_db_version)
	{
		return false;
	}

	$charset_collate = WC1C_Db()->get_charset_collate();

	$table_name = WC1C_Db()->base_prefix . 'wc1c';

	$sql = "CREATE TABLE $table_name (
	`config_id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
	`site_id` INT(11) UNSIGNED NOT NULL DEFAULT '0',
	`config_name` VARCHAR(155) NULL DEFAULT NULL,
	`config_default` TINYINT(4) NULL DEFAULT '0',
	`status` VARCHAR(50) NULL DEFAULT NULL,
	`options` TEXT NULL DEFAULT NULL,
	`schema` VARCHAR(50) NULL DEFAULT NULL,
	`date_create` VARCHAR(50) NULL DEFAULT NULL,
	`date_modify` VARCHAR(50) NULL DEFAULT NULL,
	`date_activity` VARCHAR(50) NULL DEFAULT NULL,
	PRIMARY KEY (`config_id`),
	UNIQUE INDEX `config_id` (`config_id`)
	) $charset_collate;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

	dbDelta($sql);

	add_site_option('wc1c_db_version', $wc1c_db_version);

	return true;
}

/**
 * Is WC1C api request?
 *
 * @return bool
 */
function is_wc1c_api_request()
{
	if(isset($_GET['wc1c-api']))
	{
		return true;
	}

	return false;
}

/**
 * Is WC1C admin request?
 *
 * @return bool
 */
function is_wc1c_admin_request()
{
	if(is_admin() !== false && isset($_GET['page']) && $_GET['page'] === 'wc1c')
	{
		return true;
	}

	return false;
}


/**
 * Localisation loading
 */
function wc1c_load_textdomain()
{
	/**
	 * WP 5.x or later
	 */
	if(function_exists('determine_locale'))
	{
		$locale = determine_locale();
	}
	else
	{
		$locale = is_admin() && function_exists('get_user_locale') ? get_user_locale() : get_locale();
	}

	/**
	 * Change locale from external code
	 */
	$locale = apply_filters('plugin_locale', $locale, 'wc1c');

	unload_textdomain('wc1c');
	load_textdomain('wc1c', WP_LANG_DIR . '/plugins/wc1c-' . $locale . '.mo');
	load_textdomain('wc1c', WC1C_PLUGIN_PATH . 'languages/wc1c-' . $locale . '.mo');
}