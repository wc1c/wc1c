<?php
/**
 * Admin configurations list table class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_List_Table extends Abstract_Wc1c_Admin_Table
{
	/**
	 * Configurations storage
	 *
	 * @var Wc1c_Data_Storage_Configurations
	 */
	public $storage_configurations;

	/**
	 * Wc1c_Admin_Configurations_List_Table constructor
	 */
	public function __construct()
	{
	    $params =
        [
            'singular' => 'configuration',
            'plural' => 'configurations',
            'ajax' => false
        ];

		try
		{
			$this->storage_configurations = Wc1c_Data_Storage::load('configuration');
		}
		catch(Exception $e){}

		parent::__construct($params);
	}

	/**
	 * No items found text
	 */
	public function no_items()
	{
		wc1c_get_template('configurations/list_empty.php');
	}

	/**
	 * Get a list of CSS classes for the WP_List_Table table tag
	 *
	 * @return array  - list of CSS classes for the table tag
	 */
	protected function get_table_classes()
	{
		return
        [
		    'widefat',
            'striped',
            $this->_args['plural']
        ];
	}

	/**
	 * Default print rows
	 *
	 * @param object $item
	 * @param string $column_name
	 *
	 * @return mixed
	 */
	public function column_default($item, $column_name)
	{
		switch ($column_name)
		{
			case 'configuration_id':
				return $item['configuration_id'];
			case 'schema':
				return $item['schema'];
			case 'date_create':
			case 'date_activity':
			case 'date_modify':
				return $this->pretty_columns_date($item, $column_name);
			default:
				return print_r($item, true);
		}
	}

	/**
	 * @param $item
	 * @param $column_name
	 *
	 * @return string
	 */
	private function pretty_columns_date($item, $column_name)
	{
		$date = $item[$column_name];
		$timestamp = wc1c_string_to_timestamp($date) + wc1c_timezone_offset();

		if(!empty($date))
		{
			return sprintf
			(
				__('%s <br/><span class="wc1c-time">Time: %s</span>', 'wc1c'),
				date_i18n('d/m/Y', $timestamp),
				date_i18n('H:i:s', $timestamp)
			);
		}

		return __('No activity', 'wc1c');
	}

	/**
	 * Configuration status
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_status($item)
	{
		$status = wc1c_configurations_get_statuses_label($item['status']);
		$status_return = wc1c_configurations_get_statuses_label('error');

		if($item['status'] === 'draft')
		{
			$status_return = '<span class="draft">' . $status . '</span>';
		}
		if($item['status'] === 'active')
		{
			$status_return = '<span class="active">' . $status . '</span>';
		}
		if($item['status'] === 'inactive')
		{
			$status_return = '<span class="inactive">' . $status . '</span>';
		}
		if($item['status'] === 'processing')
		{
			$status_return = '<span class="processing">' . $status . '</span>';
		}
		if($item['status'] === 'error')
		{
			$status_return = '<span class="error">' . $status . '</span>';
		}
		if($item['status'] === 'deleted')
		{
			$status_return = '<span class="deleted">' . $status . '</span>';
		}

		return $status_return;
	}

	/**
	 * Configuration name
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_name($item)
	{
		$actions =
		[
			'update' => '<a href="' . wc1c_admin_configurations_get_url('update', $item['configuration_id']) . '">' . __('Edit', 'wc1c') . '</a>',
			'remove' => '<a href="' . wc1c_admin_configurations_get_url('remove', $item['configuration_id']) . '">' . __('Remove', 'wc1c') . '</a>',
		];

		if('deleted' === $item['status'])
		{
			unset($actions['update']);
			$actions['remove'] = '<a href="' . wc1c_admin_configurations_get_url('remove', $item['configuration_id']) . '">' . __('Remove forever', 'wc1c') . '</a>';
		}

		return sprintf( '%1$s <br/> %2$s',
			/*$1%s*/
			$item['name'],
			/*$2%s*/
			$this->row_actions($actions, true)
		);
	}

	/**
	 * All columns
	 *
	 * @return array
	 */
	public function get_columns()
	{
		$columns = [];

		$columns['configuration_id'] = __('ID', 'wc1c');
		$columns['schema'] = __('Schema ID', 'wc1c');
		$columns['name'] = __('Name', 'wc1c');
		$columns['status'] = __('Status', 'wc1c');
		$columns['date_create'] = __('Create date', 'wc1c');
		$columns['date_activity'] = __('Last activity', 'wc1c');

		return $columns;
	}

	/**
	 * Sortable columns
	 *
	 * @return array
	 */
	public function get_sortable_columns()
	{
		$sortable_columns['configuration_id'] = ['configuration_id', false];
		$sortable_columns['status'] = ['status', false];

		return $sortable_columns;
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string The name of the primary column
	 */
	protected function get_default_primary_column_name()
	{
		return 'configuration_id';
	}

	/**
	 * Creates the different status filter links at the top of the table.
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function get_views()
	{
		$status_links = [];
		$current = !empty($_REQUEST['status']) ? $_REQUEST['status'] : 'all';

		// All link
		$class = $current === 'all' ? ' class="current"' :'';
		$all_url = remove_query_arg('status');

		$status_links['all'] = sprintf
		(
			'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
			$all_url,
			$class,
			__('All', 'wc1c'),
			$this->storage_configurations->count()
		);

		$statuses = wc1c_configurations_get_statuses();

		foreach($statuses as $status_key)
		{
			$count = $this->storage_configurations->count_by(
				[
					'status' => $status_key
				]
			);

			if($count === 0)
			{
				continue;
			}

			$class = $current === $status_key ? ' class="current"' :'';
			$sold_url = esc_url(add_query_arg('status', $status_key));

			$status_links[$status_key] = sprintf
			(
				'<a href="%s" %s>%s <span class="count">(%d)</span></a>',
				$sold_url,
				$class,
				wc1c_configurations_get_statuses_folder($status_key),
				$count
			);
		}

		return $status_links;
	}

	/**
	 * Build items
	 */
	public function prepare_items()
	{
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = WC1C()->settings()->get('configurations_show_per_page', 10);

		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->get_columns();
		$hidden = [];
		$sortable = $this->get_sortable_columns();

		/**
		 * REQUIRED. Finally, we build an array to be used by the class for column
		 * headers. The $this->_column_headers property takes an array which contains
		 * 3 other arrays. One for all columns, one for hidden columns, and one
		 * for sortable columns.
		 */
		$this->_column_headers = [$columns, $hidden, $sortable];

		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$offset = 0;

		if(1 < $current_page)
		{
			$offset = $per_page * ($current_page - 1);
		}

		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'configuration_id';
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';

		$configurations_args = [];

		if(array_key_exists('status', $_GET) && in_array($_GET['status'], wc1c_configurations_get_statuses(), true))
		{
			$configurations_args['status'] = $_GET['status'];
		}

		if(!empty($_REQUEST['s']))
		{
			$configurations_args['name'] =
			[
				'key' => 'name',
				'value' => sanitize_text_field($_REQUEST['s']),
				'compare_key' => 'LIKE'
			];
		}

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		if(empty($configurations_args))
		{
			$total_items = $this->storage_configurations->count();
		}
		else
		{
			$total_items = $this->storage_configurations->count_by($configurations_args);
		}

		$configurations_args['offset'] = $offset;
		$configurations_args['limit'] = $per_page;
		$configurations_args['orderby'] = $orderby;
		$configurations_args['order'] = $order;

		$this->items = $this->storage_configurations->get_data($configurations_args, ARRAY_A);

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args
		(
			[
				'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil($total_items / $per_page)
            ]
		);
	}

	/**
	 * Search box
	 *
	 * @param string $text Button text
	 * @param string $input_id Input ID
	 */
	public function search_box($text, $input_id)
	{
		if(empty($_REQUEST['s']) && !$this->has_items())
		{
			return;
		}

		$input_id .= '-search-input';

		$search_query = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';

		echo '<p class="search-box">';
		echo '<label class="screen-reader-text" for="' . esc_attr($input_id) . '">' . esc_html($text) . ':</label>';
		echo '<input type="search" id="' . esc_attr($input_id) . '" name="s" value="' . esc_attr($search_query) . '" />';
		submit_button($text, '', '', false, array('id' => 'search-submit'));
		echo '</p>';
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @param string $which
	 */
	protected function extra_tablenav($which)
	{
		if('top' === $which)
		{
			$this->views();
			$this->search_box(__( 'Search', 'wc1c' ), 'name');
		}
	}
}