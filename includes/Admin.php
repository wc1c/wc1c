<?php
/**
 * Namespace
 */
namespace Wc1c;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Digiom\WordPress\Notices\Interfaces\ManagerInterface;
use Digiom\WordPress\Notices\Manager;
use Wc1c\Admin\Configurations;
use Wc1c\Admin\Extensions;
use Wc1c\Admin\Settings;
use Wc1c\Admin\Tools;
use Wc1c\Traits\SectionsTrait;
use Wc1c\Traits\SingletonTrait;

/**
 * Admin
 *
 * @package Wc1c
 */
final class Admin
{
	use SingletonTrait;
	use SectionsTrait;

	/**
	 * @var ManagerInterface Admin notices
	 */
	private $notices;

	/**
	 * Admin constructor.
	 */
	public function __construct()
	{
		// hook
		do_action(WC1C_ADMIN_PREFIX . 'before_loading');

		$this->notices();

		if('yes' === wc1c()->settings()->get('admin_interface', 'yes'))
		{
			Admin\Columns\Init::instance();
		}

		add_action('admin_menu', [$this, 'addMenu'], 30);

		if(wc1c()->request()->isWc1cAdmin())
		{
			add_action('init', [$this, 'init'], 10);
			add_action('admin_enqueue_scripts', [$this, 'initStyles']);

			Admin\Helps\Init::instance();
		}

		if(defined('WC1C_PLUGIN_NAME'))
		{
			add_filter('plugin_action_links_' . WC1C_PLUGIN_NAME, [$this, 'linksLeft']);
		}

		// hook
		do_action(WC1C_ADMIN_PREFIX . 'after_loading');
	}

	/**
	 * Admin notices
	 *
	 * @return ManagerInterface
	 */
	public function notices()
	{
		if(empty($this->notices))
		{
			$args =
			[
				'auto_save' => true,
				'admin_notices' => false,
				'user_admin_notices' => false,
				'network_admin_notices' => false
			];

			$this->notices = new Manager(WC1C_ADMIN_PREFIX . 'notices', $args);
		}

		return $this->notices;
	}

	/**
	 * Init menu
	 */
	public function addMenu()
	{
		add_submenu_page
		(
			'woocommerce',
			__('Integration with 1C', 'wc1c'),
			__('Integration with 1C', 'wc1c'),
			'manage_woocommerce',
			'wc1c',
			[$this, 'route']
		);
	}

	/**
	 * Initialization
	 */
	public function init()
	{
		// hook
		do_action(WC1C_ADMIN_PREFIX . 'before_init');

		$default_sections['configurations'] =
		[
			'title' => __('Configurations', 'wc1c'),
			'visible' => true,
			'callback' => [Configurations::class, 'instance']
		];

		$default_sections['tools'] =
		[
			'title' => __('Tools', 'wc1c'),
			'visible' => true,
			'callback' => [Tools::class, 'instance']
		];

		$default_sections['settings'] =
		[
			'title' => __('Settings', 'wc1c'),
			'visible' => true,
			'callback' => [Settings::class, 'instance']
		];

		$default_sections['extensions'] =
		[
			'title' => __('Extensions', 'wc1c'),
			'visible' => true,
			'callback' => [Extensions::class, 'instance']
		];

		$this->initSections($default_sections);
		$this->setCurrentSection('configurations');

		// hook
		do_action(WC1C_ADMIN_PREFIX . 'after_init');
	}

	/**
	 * Styles
	 */
	public function initStyles()
	{
		wp_enqueue_style(WC1C_ADMIN_PREFIX . 'main', WC1C_PLUGIN_URL . 'assets/css/main.css');
	}

	/**
	 * Route sections
	 */
	public function route()
	{
		$sections = $this->getSections();
		$current_section = $this->initCurrentSection();

		if(!array_key_exists($current_section, $sections) || !isset($sections[$current_section]['callback']))
		{
			add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'wrapError']);
		}
		else
		{
			add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'wrapHeader'], 3);
			add_action(WC1C_ADMIN_PREFIX . 'show', [$this, 'wrapSections'], 7);

			$callback = $sections[$current_section]['callback'];

			if(is_callable($callback, false, $callback_name))
			{
				$callback_name();
			}
		}

		wc1c()->templates()->getTemplate('wrap.php');
	}

	/**
	 * Error
	 */
	public function wrapError()
	{
		wc1c()->templates()->getTemplate('error.php');
	}

	/**
	 * Header
	 */
	public function wrapHeader()
	{
		wc1c()->templates()->getTemplate('header.php');
	}

	/**
	 * Sections
	 */
	public function wrapSections()
	{
		wc1c()->templates()->getTemplate('sections.php');
	}

	/**
	 * Setup left links
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function linksLeft($links)
	{
		return array_merge(['site' => '<a href="' . admin_url('admin.php?page=wc1c') . '">' . __('Settings', 'wc1c') . '</a>'], $links);
	}
}