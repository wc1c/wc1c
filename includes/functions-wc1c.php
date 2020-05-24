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
	if(is_admin() !== false && wc1c_get_var($_GET['page'], '') === 'wc1c')
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

/**
 * Get data if set, otherwise return a default value or null
 * Prevents notices when data is not set
 *
 * @param mixed $var variable
 * @param string $default default value
 *
 * @return mixed
 */
function wc1c_get_var(&$var, $default = null)
{
	return isset($var) ? $var : $default;
}

/**
 * Define constant if not already set
 *
 * @param string $name constant name
 * @param string|bool $value constant value
 */
function wc1c_define($name, $value)
{
	if(!defined($name))
	{
		define($name, $value);
	}
}

/**
 * Get other templates
 *
 * @param string $template_name template name
 * @param array $args arguments (default: array)
 * @param string $template_path template path (default: '')
 * @param string $default_path default path (default: '')
 */
function wc1c_get_template($template_name, $args = [], $template_path = '', $default_path = '')
{
	$located = wc1c_locate_template($template_name, $template_path, $default_path);

	if(!file_exists($located))
	{
		return;
	}

	$located = apply_filters('wc1c_get_template', $located, $template_name, $args, $template_path, $default_path);

	do_action('wc1c_before_template_part', $template_name, $template_path, $located, $args);

	include $located;

	do_action('wc1c_after_template_part', $template_name, $template_path, $located, $args);
}

/**
 * Get template part
 *
 * @param mixed $slug Template slug
 * @param string $name Template name (default: '')
 */
function wc1c_get_template_part($slug, $name = '')
{
	$template = '';

	// Look in yourtheme/slug-name.php and yourtheme/wc1c/slug-name.php
	if($name)
	{
		$template = locate_template(array("{$slug}-{$name}.php", 'wc1c/' . "{$slug}-{$name}.php"));
	}

	// Get default slug-name.php
	if(!$template && $name && file_exists(WC1C_PLUGIN_PATH . "templates/{$slug}-{$name}.php"))
	{
		$template = WC1C_PLUGIN_PATH . "templates/{$slug}-{$name}.php";
	}

	// If template file doesn't exist, look in yourtheme/slug.php and yourtheme/wc1c/slug.php
	if(!$template)
	{
		$template = locate_template(array("{$slug}.php", 'wc1c/' . "{$slug}.php"));
	}

	// Allow 3rd party plugins to filter template file from their plugin
	$template = apply_filters('wc1c_get_template_part', $template, $slug, $name);

	if($template)
	{
		load_template($template, false);
	}
}

/**
 * Like wc1c_get_template, but returns the HTML instead of outputting
 *
 * @param string $template_name template name
 * @param array $args arguments (default: array)
 * @param string $template_path template path (default: '')
 * @param string $default_path default path (default: '')
 *
 * @return string
 * @see wc1c_get_template
 */
function wc1c_get_template_html($template_name, $args = [], $template_path = '', $default_path = '')
{
	ob_start();
	wc1c_get_template($template_name, $args, $template_path, $default_path);

	return ob_get_clean();
}

/**
 * Locate a template and return the path for inclusion
 *
 * This is the load order:
 *
 * yourtheme/$template_path/$template_name
 * yourtheme/$template_name
 * $default_path/$template_name
 *
 * @param string $template_name template name
 * @param string $template_path template path (default: '')
 * @param string $default_path default path (default: '')
 * @return string
 */
function wc1c_locate_template($template_name, $template_path = '', $default_path = '')
{
	if(!$template_path)
	{
		$template_path = 'wc1c/';
	}

	if(!$default_path)
	{
		$default_path = WC1C_PLUGIN_PATH . 'templates/';
	}

	// Look within passed path within the theme - this is priority
	$template = locate_template
	(
		array
		(
			trailingslashit($template_path) . $template_name,
			$template_name,
		)
	);

	// Get default template/
	if(!$template)
	{
		$template = $default_path . $template_name;
	}

	// Return what we found
	return apply_filters('wc1c_locate_template', $template, $template_name, $template_path);
}

/**
 * Переводит значение из килобайт, мегабат и гигабайт в байты
 *
 * @param $size
 *
 * @return float|int
 */
function wc1c_convert_size($size)
{
	if(empty($size))
	{
		return 0;
	}

	$type = $size{strlen($size) - 1};

	if(!is_numeric($type))
	{
		$size = (int) $size;

		switch($type)
		{
			case 'K':
				$size = $size * 1024;
				break;
			case 'M':
				$size = $size * 1024 * 1024;
				break;
			case 'G':
				$size = $size * 1024 * 1024 * 1024;
				break;
		}

		return $size;
	}

	return (int)$size;
}

/**
 * Get normal configuration status
 *
 * @param null|string $status
 *
 * @return array|string|false
 */
function wc1c_get_configurations_status_print($status = null)
{
	$statuses =
	[
		'draft' => __('Draft', 'wc1c'),
		'active' => __('Active', 'wc1c'),
		'inactive' => __('Inactive', 'wc1c'),
		'error' => __('Error', 'wc1c'),
		'processing' => __('Processing', 'wc1c'),
	];

	if(null !== $status)
	{
		if(array_key_exists($status, $statuses))
		{
			return $statuses[$status];
		}

		return false;
	}

	return $statuses;
}

/**
 * Wrapper for set_time_limit to see if it is enabled
 *
 * @param int $limit time limit
 *
 * @return bool
 */
function wc1c_set_time_limit($limit = 0)
{
	if(function_exists('set_time_limit') && !ini_get('safe_mode') && false === strpos(ini_get('disable_functions'), 'set_time_limit'))
	{
		@set_time_limit($limit);
		
		return true;
	}

	return false;
}