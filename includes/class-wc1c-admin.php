<?php
/**
 * Final Admin class
 * Configurations, settings, schemas, tools, reports and more
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
	 * Admin sections
	 *
	 * @var array
	 */
	private $sections = [];

	/**
	 * Admin messages
	 *
	 * @var array
	 */
	private $messages = [];

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

		$this->load_current_section();
		$this->init_includes();
		$this->init_hooks();

		// hook
		do_action('wc1c_admin_after_loading');
	}

	/**
	 * Get current admin section
	 *
	 * @return string
	 */
	public function get_current_section()
	{
		return $this->current_section;
	}

	/**
	 * Set current admin section
	 *
	 * @param string $current_section
	 */
	public function set_current_section($current_section)
	{
		$this->current_section = $current_section;
	}

	/**
	 * Loading current admin section
	 *
	 * @return string
	 */
	public function load_current_section()
	{
		$current_section = !empty($_GET['section']) ? sanitize_title($_GET['section']) : 'configurations';
		$this->set_current_section($current_section);

		return $this->get_current_section();
	}

	/**
	 * Actions and filters
	 */
	private function init_hooks()
	{
		/**
		 * Init
		 */
		add_action('init', array($this, 'init'), 10);

		/**
		 * Plugin lists
		 */
		add_filter('plugin_action_links_' . WC1C_PLUGIN_NAME, array($this, 'links_left'));

		/**
		 * Styles
		 */
		add_action('admin_enqueue_scripts', array($this, 'init_styles'));

		/**
		 * Add admin menu
		 */
		add_action('admin_menu', array($this, 'menu'), 30);

		/**
		 * Admin header
		 */
		add_action('wc1c_admin_page', array($this, 'page_header'), 10, 1);
	}

	/**
	 * Initialization
	 */
	public function init()
	{
		// hook
		do_action('wc1c_admin_before_init');

		/**
		 * Sections
		 */
		$this->init_sections();

		/**
		 * Admin inject
		 */
		if(WC1C()->settings()->get('admin_inject', 'yes') === 'yes')
		{
			try
			{
				$inject = new Wc1c_Admin_Inject();
			}
			catch(Exception $e){}
		}

		// hook
		do_action('wc1c_admin_after_init');
	}

	/**
	 * Initialization sections
	 */
	public function init_sections()
	{
		/**
		 * Configurations
		 */
		$default_sections['configurations'] = array
		(
			'title' => __('Configurations', 'wc1c'),
			'callback' => array($this, 'page_configurations')
		);
		if('configurations' === $this->get_current_section())
		{
			new Wc1c_Admin_Configurations();
		}

		/**
		 * Settings
		 */
		$default_sections['settings'] = array
		(
			'title' => __('Settings', 'wc1c'),
			'callback' => array($this, 'page_settings')
		);
		if('settings' === $this->get_current_section())
		{
			new Wc1c_Admin_Settings();
		}

		/**
		 * Tools
		 */
		$default_sections['tools'] = array
		(
			'title' => __('Tools', 'wc1c'),
			'callback' => array($this, 'page_tools')
		);
		if('tools' === $this->get_current_section())
		{
			new Wc1c_Admin_Tools();
		}

		/**
		 * Reports
		 */
		$default_sections['reports'] = array
		(
			'title' => __('Reports', 'wc1c'),
			'callback' => array($this, 'page_reports')
		);
		if('reports' === $this->get_current_section())
		{
			new Wc1c_Admin_Reports();
		}

		/**
		 * Schemas
		 */
		$default_sections['schemas'] = array
		(
			'title' => __('Schemas', 'wc1c'),
			'callback' => array($this, 'page_schemas')
		);
		if('schemas' === $this->get_current_section())
		{
			new Wc1c_Admin_Schemas();
		}

		/**
		 * Extensions
		 */
		$default_sections['extensions'] = array
		(
			'title' => __('Extensions', 'wc1c'),
			'callback' => array($this, 'page_extensions')
		);
		if('extensions' === $this->get_current_section())
		{
			new Wc1c_Admin_Extensions();
		}

		$this->set_sections(apply_filters('wc1c_admin_init_sections', $default_sections));

		/**
		 * Init actions with page
		 */
		foreach($this->get_sections() as $section_key => $section)
		{
			if(is_callable($section['callback']))
			{
				add_action('wc1c_admin_page_' . $section_key, $section['callback'], 10, 1);
			}
		}
	}

	/**
	 * Get admin sections
	 *
	 * @return array
	 */
	public function get_sections()
	{
		return $this->sections;
	}

	/**
	 * Set admin sections
	 *
	 * @param array $sections
	 */
	public function set_sections($sections)
	{
		$this->sections = $sections;
	}

	/**
	 * Add menu
	 */
	public function menu()
	{
		add_submenu_page
		(
			'woocommerce',
			__('Integration with 1C', 'wc1c'),
			__('Integration with 1C', 'wc1c'),
			'edit_pages',
			'wc1c',
			array
			(
				$this,
				'route_sections'
			)
		);
	}

	/**
	 * Route
	 */
	public function route_sections()
	{
		$current_section = $this->get_current_section();

		// hook
		do_action('wc1c_admin_page', $current_section);

		if(array_key_exists($current_section, $this->get_sections()))
		{
			// hook
			do_action('wc1c_admin_page_' . $current_section, $current_section);
		}
		else
		{
			wc1c_get_template('page_404.php');
		}
	}

	/**
	 * Show header
	 */
	public function page_header()
	{
		wc1c_get_template('admin_header.php');
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
		return array_merge(array('site' => '<a href="' . admin_url('admin.php?page=wc1c') . '">' . __('Settings', 'wc1c') . '</a>'), $links);
	}

	/**
	 * Configurations
	 *
	 * @return void
	 */
	public function page_configurations()
	{
		wc1c_get_template('page_configurations.php');
	}

	/**
	 * Schemas
	 *
	 * @return void
	 */
	public function page_schemas()
	{
		wc1c_get_template('page_schemas.php');
	}

	/**
	 * Reports
	 *
	 * @return void
	 */
	public function page_reports()
	{
		wc1c_get_template('page_reports.php');
	}

	/**
	 * Tools
	 *
	 * @return void
	 */
	public function page_tools()
	{
		wc1c_get_template('page_tools.php');
	}

	/**
	 * Extensions
	 *
	 * @return void
	 */
	public function page_extensions()
	{
		wc1c_get_template('page_extensions.php');
	}

	/**
	 * Settings
	 *
	 * @return void
	 */
	public function page_settings()
	{
		wc1c_get_template('page_settings.php');
	}

	/**
	 * Create navigation
	 *
	 * @return string
	 */
	public function page_tabs() //todo: template
	{
		$nav = '<nav class="nav-tab-wrapper woo-nav-tab-wrapper">';

		foreach($this->get_sections() as $tab_key => $tab_name)
		{
			if($this->get_current_section() == $tab_key)
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
	 * Include required files
	 *
	 * @return void
	 */
	private function init_includes()
	{
		// hook
		do_action('wc1c_admin_before_includes');

		/**
		 * Abstract classes
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/abstracts/abstract-class-wc1c-admin-form.php';

		/**
		 * Other
		 */
		include_once WC1C_PLUGIN_PATH . 'includes/admin/class-wc1c-admin-inject.php';

		/**
		 * Configurations section
		 */
		if('configurations' === $this->get_current_section())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/admin/class-wc1c-admin-configurations.php';
		}

		/**
		 * Settings section
		 */
		if('settings' === $this->get_current_section())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/admin/class-wc1c-admin-settings.php';
		}

		/**
		 * Schemas section
		 */
		if('schemas' === $this->get_current_section())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/admin/class-wc1c-admin-schemas.php';
		}

		/**
		 * Tools section
		 */
		if('tools' === $this->get_current_section())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/admin/class-wc1c-admin-tools.php';
		}

		/**
		 * Extensions section
		 */
		if('extensions' === $this->get_current_section())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/admin/class-wc1c-admin-extensions.php';
		}

		/**
		 * Reports section
		 */
		if('reports' === $this->get_current_section())
		{
			include_once WC1C_PLUGIN_PATH . 'includes/admin/class-wc1c-admin-reports.php';
		}

		// hook
		do_action('wc1c_admin_after_includes');
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
	public function format_message($type, $message, $args = array())
	{
		if($type == 'error')
		{
			return '<div class="updated settings-error notice error is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __( 'Close', 'wc1c' ) . '</span></button></div>';
		}

		if($type == 'update')
		{
			return '<div class="updated settings-update notice is-dismissible"><p><strong>' . $message . '</strong></p><button class="notice-dismiss" type="button"><span class="screen-reader-text">' . __( 'Close', 'wc1c' ) . '</span></button></div>';
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
		$this->messages[] = array
		(
			'type' => $type,
			'message' => $message
		);
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
			foreach ($messages as $message_key => $message_data)
			{
				echo $this->format_message($message_data['type'], $message_data['message']);
			}
		}
	}
}