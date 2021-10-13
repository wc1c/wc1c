<?php
/**
 * Final Admin class
 * Configurations, settings, tools, extensions and more
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

final class Wc1c_Admin
{
	/**
	 * Traits
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Admin messages
	 *
	 * @var array
	 */
	private $messages = [];

	/**
	 * Admin sections
	 *
	 * @var array
	 */
	private $sections = [];

	/**
	 * Current admin section
	 *
	 * @var string
	 */
	private $current_section = 'configurations';

	/**
	 * Wc1c_Admin constructor
	 */
	public function __construct()
	{
		// hook
		do_action('wc1c_admin_before_loading');

		$this->init_hooks();

		// hook
		do_action('wc1c_admin_after_loading');
	}

	/**
	 * Actions and filters
	 */
	private function init_hooks()
	{
		add_action('admin_menu', [$this, 'init_menu'], 30);
		add_action('init', [$this, 'init'], 10);
		add_action('admin_enqueue_scripts', [$this, 'init_styles']);

		if(defined('WC1C_PLUGIN_NAME'))
		{
			add_filter('plugin_action_links_' . WC1C_PLUGIN_NAME, [$this, 'links_left']);
		}
	}

	/**
	 * Get current section
	 *
	 * @return string
	 */
	public function get_current_section()
	{
		return apply_filters('wc1c_admin_get_current_section', $this->current_section);
	}

	/**
	 * Set current section
	 *
	 * @param string $current_section
	 */
	public function set_current_section($current_section)
	{
		// hook
		$current_section = apply_filters('wc1c_admin_set_current_section', $current_section);

		$this->current_section = $current_section;
	}

	/**
	 * Initialize current section
	 *
	 * @return string
	 */
	public function init_current_section()
	{
		$current_section = !empty($_GET['section']) ? sanitize_title($_GET['section']) : 'configurations';

		$this->set_current_section($current_section);

		return $this->get_current_section();
	}

	/**
	 * Initialization
	 */
	public function init()
	{
		// hook
		do_action('wc1c_admin_before_init');

		/**
		 * Admin inject
		 */
		if('yes' === WC1C()->settings()->get('admin_inject', 'yes'))
		{
			Wc1c_Admin_Inject::instance();
		}

		if(is_wc1c_admin_request())
		{
			/**
			 * Helps
			 */
			Wc1c_Admin_Help::instance();

			/**
			 * Sections
			 */
			$this->init_current_section();
			$this->init_sections();
		}

		// hook
		do_action('wc1c_admin_after_init');
	}

	/**
	 * Initialization sections
	 */
	public function init_sections()
	{
		$default_sections['configurations'] =
		[
			'title' => __('Configurations', 'wc1c'),
			'callback' => ['Wc1c_Admin_Configurations', 'instance']
		];

		$default_sections['tools'] =
		[
			'title' => __('Tools', 'wc1c'),
			'callback' => ['Wc1c_Admin_Tools', 'instance']
		];

		$default_sections['settings'] =
		[
			'title' => __('Settings', 'wc1c'),
			'callback' => ['Wc1c_Admin_Settings', 'instance']
		];

		$default_sections['extensions'] =
		[
			'title' => __('Extensions', 'wc1c'),
			'callback' => ['Wc1c_Admin_Extensions', 'instance']
		];

		// hook
		$sections = apply_filters('wc1c_admin_init_sections', $default_sections);

		if(empty($sections))
		{
			$sections = $default_sections;
		}

		$this->set_sections($sections);
	}

	/**
	 * Get admin sections
	 *
	 * @return array
	 */
	public function get_sections()
	{
		return apply_filters('wc1c_admin_get_sections', $this->sections);
	}

	/**
	 * Set admin sections
	 *
	 * @param array $sections
	 */
	public function set_sections($sections)
	{
		// hook
		$sections = apply_filters('wc1c_admin_set_sections', $sections);

		$this->sections = $sections;
	}

	/**
	 * Add menu
	 */
	public function init_menu()
	{
		add_submenu_page
		(
			'woocommerce',
			__('Integration with 1C', 'wc1c'),
			__('Integration with 1C', 'wc1c'),
			'manage_woocommerce',
			'wc1c', [$this, 'route_sections']
		);
	}

	/**
	 * Route
	 */
	public function route_sections()
	{
		$sections = $this->get_sections();
		$current_section = $this->get_current_section();

		if(!array_key_exists($current_section, $sections))
		{
			wc1c_get_template('page_404.php');
			return;
		}

		$callback = $sections[$current_section]['callback'];

		if(is_callable($callback, false, $callback_name))
		{
			$callback_name();
		}

		wc1c_get_template('page.php');
	}

	/**
	 * Admin styles
	 */
	public function init_styles()
	{
		if(is_wc1c_admin_request())
		{
			wp_enqueue_style('wc1c-admin-styles', WC1C_PLUGIN_URL . 'assets/css/main.css');
		}
	}

	/**
	 * Setup left links
	 *
	 * @param $links
	 *
	 * @return array
	 */
	public function links_left($links)
	{
		return array_merge(['site' => '<a href="' . admin_url('admin.php?page=wc1c') . '">' . __('Settings', 'wc1c') . '</a>'], $links);
	}

	/**
	 * Navigations
	 *
	 * @return string
	 */
	public function page_tabs() //todo: template
	{
		$nav = '<nav class="nav-tab-wrapper woo-nav-tab-wrapper">';

		foreach($this->get_sections() as $tab_key => $tab_name)
		{
			if($tab_key === $this->get_current_section())
			{
				$nav .= '<a href="' . admin_url('admin.php?page=wc1c&section=' . $tab_key) . '" class="nav-tab nav-tab-active">' . $tab_name['title'] . '</a>';
			}
			else
			{
				$nav .= '<a href="' . admin_url('admin.php?page=wc1c&section=' . $tab_key) . '" class="nav-tab ">' . $tab_name['title'] . '</a>';
			}
		}

		$nav .= '</nav>';

		return $nav;
	}

	/**
	 * Format message to notice in admin
	 *
	 * @param $type
	 * @param $message
	 * @param $args
	 *
	 * @return string
	 */
	public function format_message($type, $message, $args = [])
	{
		if($type === 'error')
		{
			return '<div id="message" class="settings-error error updated notice is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __( 'Close', 'wc1c' ) . '</span></button></div>';
		}

		if($type === 'update')
		{
			return '<div id="message" class="settings-update update updated notice is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __( 'Close', 'wc1c' ) . '</span></button></div>';
		}

		return $message;
	}

	/**
	 * Add admin messages
	 *
	 * @param $type
	 * @param $message
	 */
	public function add_message($type, $message)
	{
		$this->messages[] =
		[
			'type' => $type,
			'message' => $message
		];
	}

	/**
	 * Set admin messages
	 *
	 * @param $messages
	 */
	public function set_messages($messages)
	{
		$this->messages = $messages;
	}

	/**
	 * Get admin messages
	 *
	 * @return array
	 */
	public function get_messages()
	{
		return $this->messages;
	}

	/**
	 * Show messages in admin
	 */
	public function print_messages()
	{
		$messages = $this->get_messages();

		if(count($messages) > 0)
		{
			foreach($messages as $message_key => $message_data)
			{
				echo $this->format_message($message_data['type'], $message_data['message']);
			}
		}
	}
}