<?php namespace Digiom\Woplucore;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Interfaces\Viewable;
use Digiom\Woplucore\Traits\SingletonTrait;

/**
 * Views
 *
 * @package Digiom\Woplucore
 */
class Views implements Viewable
{
	use SingletonTrait;

	/**
	 * @var string Unique slug
	 */
	protected $slug;

	/**
	 * @var string Path for plugin dir
	 */
	protected $plugin_dir;

	/**
	 * Views constructor.
	 *
	 * @param string $slug
	 * @param string $plugin_dir
	 */
	public function __construct(string $slug = '', string $plugin_dir = '')
	{
		$this->setSlug($slug);
		$this->setPluginDir($plugin_dir);

		do_action($this->getSlug() . '_views_loaded');
	}

	/**
	 * @return string
	 */
	public function getSlug(): string
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 *
	 * @return Views
	 */
	public function setSlug(string $slug): Views
	{
		$this->slug = $slug;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getPluginDir(): string
	{
		return $this->plugin_dir;
	}

	/**
	 * @param string $plugin_dir
	 *
	 * @return Views
	 */
	public function setPluginDir(string $plugin_dir): Views
	{
		$this->plugin_dir = $plugin_dir;

		return $this;
	}

	/**
	 * Outputs a "back" link so admin screens can easily jump back a page
	 *
	 * @param string $label title of the page to return to.
	 * @param string $url URL of the page to return to.
	 */
	public function adminBackLink(string $label, string $url)
	{
		echo '<h2 class="back-link" style="margin-bottom: 15px;margin-top: 15px;">' . esc_attr($label) . '<small class="wc-admin-breadcrumb" style="margin-left: 10px;"><a href="' . esc_url($url) . '" aria-label="' . esc_attr($label) . '"> &#x2934;</a></small></h2>';
	}

	/**
	 * Get views
	 *
	 * @param string $template_name template name
	 * @param array $args arguments (default: array)
	 * @param string $template_path template path (default: '')
	 * @param string $default_path default path (default: '')
	 */
	public function getView(string $template_name, array $args = [], string $template_path = '', string $default_path = '')
	{
		$located = $this->locateView($template_name, $template_path, $default_path);

		if(!file_exists($located))
		{
			return;
		}

		$located = apply_filters($this->getSlug() . '_views_get_view', $located, $template_name, $args, $template_path, $default_path);

		do_action($this->getSlug() . '_views_get_view_before', $template_name, $template_path, $located, $args);

		include $located;

		do_action($this->getSlug() . '_views_get_view_after', $template_name, $template_path, $located, $args);
	}

	/**
	 * Get view part
	 *
	 * @param string $slug Template slug
	 * @param string $name Template name (default: '')
	 */
	public function getViewPart(string $slug, string $name = '')
	{
		$template = '';

		// Look in yourtheme/{slug}/slug-name.php
		if($name)
		{
			$template = locate_template([$this->getSlug() . '/' . "{$slug}-{$name}.php"]);
		}

		// Get default slug-name.php
		if(!$template && $name && file_exists($this->getPluginDir() . "views/{$slug}-{$name}.php"))
		{
			$template = $this->getPluginDir() . "views/{$slug}-{$name}.php";
		}

		// If template file doesn't exist, look in yourtheme/{slug}/slug.php
		if(!$template)
		{
			$template = locate_template([$this->getSlug() . '/' . "{$slug}.php"]);
		}

		// Allow 3rd party plugins to filter template file from their plugin
		$template = apply_filters($this->getSlug() . '_views_get_view_part', $template, $slug, $name);

		if($template)
		{
			load_template($template, false);
		}
	}

	/**
	 * Like views()->getView, but returns the HTML instead of outputting
	 *
	 * @param string $template_name template name
	 * @param array $args arguments (default: array)
	 * @param string $template_path template path (default: '')
	 * @param string $default_path default path (default: '')
	 *
	 * @return string
	 */
	public function getViewHtml(string $template_name, array $args = [], string $template_path = '', string $default_path = ''): string
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
	 * yourtheme/{slug}/$template_name
	 * $default_path/$template_name
	 *
	 * @param string $template_name template name
	 * @param string $template_path template path (default: '')
	 * @param string $default_path default path (default: '')
	 *
	 * @return string
	 */
	public function locateView(string $template_name, string $template_path = '', string $default_path = ''): string
	{
		$template = false;

		if(!$template_path)
		{
			$template_path = $this->getSlug();
		}

		if(!$default_path)
		{
			$default_path = $this->getPluginDir() . 'views/';
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
		return apply_filters($this->getSlug() . '_views_locate_view', $template, $template_name, $template_path);
	}
}