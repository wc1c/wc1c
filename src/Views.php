<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Wc1c\Traits\SingletonTrait;

/**
 * Views
 *
 * @package Wc1c
 */
final class Views
{
	use SingletonTrait;

	/**
	 * Views constructor.
	 */
	public function __construct()
	{
		// hook
		do_action(WC1C_PREFIX . 'views_loaded');
	}

	/**
	 * Outputs a "back" link so admin screens can easily jump back a page
	 *
	 * @param string $label title of the page to return to.
	 * @param string $url URL of the page to return to.
	 */
	public function adminBackLink($label, $url)
	{
		echo '<h2 style="margin-bottom: 15px;margin-top: 15px;">' . esc_attr($label) . '<small class="wc-admin-breadcrumb" style="margin-left: 10px;"><a href="' . esc_url($url) . '" aria-label="' . esc_attr($label) . '"> &#x2934;</a></small></h2>';
	}

	/**
	 * Get views
	 *
	 * @param string $template_name template name
	 * @param array $args arguments (default: array)
	 * @param string $template_path template path (default: '')
	 * @param string $default_path default path (default: '')
	 */
	public function getView($template_name, $args = [], $template_path = '', $default_path = '')
	{
		$located = $this->locateView($template_name, $template_path, $default_path);

		if(!file_exists($located))
		{
			return;
		}

		$located = apply_filters(WC1C_PREFIX . 'get_view', $located, $template_name, $args, $template_path, $default_path);

		do_action(WC1C_PREFIX . 'get_view_before', $template_name, $template_path, $located, $args);

		include $located;

		do_action(WC1C_PREFIX . 'get_view_after', $template_name, $template_path, $located, $args);
	}

	/**
	 * Get view part
	 *
	 * @param mixed $slug Template slug
	 * @param string $name Template name (default: '')
	 */
	public function getViewPart($slug, $name = '')
	{
		$template = '';

		// Look in yourtheme/wc1c/slug-name.php
		if($name)
		{
			$template = locate_template(['wc1c/' . "{$slug}-{$name}.php"]);
		}

		// Get default slug-name.php
		if(!$template && $name && file_exists(WC1C_PLUGIN_PATH . "views/{$slug}-{$name}.php"))
		{
			$template = WC1C_PLUGIN_PATH . "views/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/wc1c/slug.php
		if(!$template)
		{
			$template = locate_template(['wc1c/' . "{$slug}.php"]);
		}

		// Allow 3rd party plugins to filter template file from their plugin
		$template = apply_filters(WC1C_PREFIX . 'get_view_part', $template, $slug, $name);

		if($template)
		{
			load_template($template, false);
		}
	}

	/**
	 * Like wc1c()->views()->getTemplate, but returns the HTML instead of outputting
	 *
	 * @param string $template_name template name
	 * @param array $args arguments (default: array)
	 * @param string $template_path template path (default: '')
	 * @param string $default_path default path (default: '')
	 *
	 * @return string
	 */
	public function getViewHtml($template_name, $args = [], $template_path = '', $default_path = '')
	{
		ob_start();
		$this->getView($template_name, $args, $template_path, $default_path);

		return ob_get_clean();
	}

	/**
	 * Locate a template and return the path for inclusion
	 *
	 * This is the load order:
	 *
	 * yourtheme/$template_path/$template_name
	 * yourtheme/wc1c/$template_name
	 * $default_path/$template_name
	 *
	 * @param string $template_name template name
	 * @param string $template_path template path (default: '')
	 * @param string $default_path default path (default: '')
	 *
	 * @return string
	 */
	public function locateView($template_name, $template_path = '', $default_path = '')
	{
		$template = false;

		if(!$template_path)
		{
			$template_path = 'wc1c';
		}

		if(!$default_path)
		{
			$default_path = WC1C_PLUGIN_PATH . 'views/';
		}

		if($template_path && file_exists(trailingslashit($template_path) . $template_name))
		{
			$template = trailingslashit($template_path) . $template_name;
		}

		// Get default views/
		if(!$template)
		{
			$template = $default_path . $template_name;
		}

		// Return what we found
		return apply_filters(WC1C_PREFIX . 'locate_view', $template, $template_name, $template_path);
	}
}