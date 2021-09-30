<?php
/**
 * Admin configurations table class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_List_Table extends Wc1c_Admin_Abstract_Table
{
	/**
	 * Wc1c_Admin_Configurations_List_Table constructor
	 */
	public function __construct()
	{
	    $params = array
        (
            'singular' => 'configuration',
            'plural' => 'configurations',
            'ajax' => false
        );

		parent::__construct($params);
	}

	/**
	 * No items found text
	 */
	public function no_items()
	{
		esc_html_e( 'Configurations not found. Add a new configuration via the menu.', 'wc1c' );
	}

	/**
	 * Get a list of CSS classes for the WP_List_Table table tag
	 *
	 * @return array  - list of CSS classes for the table tag
	 */
	protected function get_table_classes()
	{
		return array
        (
		    'widefat',
            'striped',
            $this->_args['plural']
        );
	}

	/**
	 * Print rows
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
			case 'config_id':
				return $item['config_id'];
			case 'schema':
				return $item['schema'];
			case 'status':

				$status = wc1c_get_configurations_status_print($item['status']);
				$status_return = wc1c_get_configurations_status_print('error');

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
					$status_return = '<span class="inactive">' . $status . '</span>';
				}

				return $status_return;

			case 'date_create':
			case 'date_activity':
			case 'date_modify':
			return sprintf(__('%s <br/><span class="wc1c-time">Time: %s</span>', 'wc1c'), date_i18n('Y-m-d', strtotime($item[$column_name])), date_i18n('H:i:s', strtotime($item[$column_name])));

			default:
				return print_r($item, true);
		}
	}

	/**
	 * @param $item
	 *
	 * @return string
	 */
	public function column_config_name($item)
	{
		//Build row actions
		$actions = array
		(
			'update'   => '<a href="' . wc1c_admin_get_configuration_url('update', $item['config_id']) . '">' . __('Edit', 'wc1c') . '</a>',
			'remove' => '<a href="' . wc1c_admin_get_configuration_url('remove', $item['config_id']) . '">' . __('Remove', 'wc1c') . '</a>',
		);

		//Return the title contents
		return sprintf( '%1$s <br/> %2$s',
			/*$1%s*/
			$item['config_name'],
			/*$2%s*/
			$this->row_actions($actions, true)
		);
	}

	/**
	 * Get an associative array ( option_name => option_title ) with the list
	 * of bulk actions available on this table
	 *
	 * @return array
	 */
	protected function get_bulk_actions()
	{
		return [];
	}

	/**
	 * All columns
	 *
	 * @return array
	 */
	public function get_columns()
	{
		$columns = [];

		$columns['config_id'] = __('ID', 'wc1c');
		$columns['schema'] = __('Schema ID', 'wc1c');
		$columns['config_name'] = __('Name', 'wc1c');
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
		$sortable_columns['config_id'] = array('config_id', false);
		$sortable_columns['status'] = array('status', false);

		return $sortable_columns;
	}

	/**
	 * Gets the name of the primary column.
	 *
	 * @return string The name of the primary column
	 */
	protected function get_default_primary_column_name()
	{
		return 'config_name';
	}

	/**
	 * Get an associative array ( id => link ) with the list
	 * of views available on this table
	 *
	 * @return array
	 */
	protected function get_views()
	{
		return [
			'a' => 'b',
			'v' => 'd'
		];
	}

	/**
	 *
	 */
	public function prepare_items()
	{
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = 10;

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
		$this->_column_headers = array( $columns, $hidden, $sortable );

		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$orderby = (!empty($_REQUEST['orderby'])) ? $_REQUEST['orderby'] : 'config_id';
		$order = (!empty($_REQUEST['order'])) ? $_REQUEST['order'] : 'desc';

		if($order !== 'desc' && $order !== 'asc')
        {
            $order = 'desc';
        }

		$sortable_columns = $this->get_sortable_columns();

		if($orderby !== 'config_id' && !isset($sortable_columns[$orderby]))
        {
            $orderby = 'config_id';
        }

		$data = WC1C_Db()->get_results( "SELECT * FROM " . WC1C_Db()->prefix . "wc1c ORDER BY {$orderby} {$order}", ARRAY_A );

		/**
		 * REQUIRED for pagination. Let's figure out what page the user is currently
		 * looking at. We'll need this later, so you should always include it in
		 * your own package classes.
		 */
		$current_page = $this->get_pagenum();

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		$total_items = count($data);

		/**
		 * The WP_List_Table class does not handle pagination for us, so we need
		 * to ensure that the data is trimmed to only the current page. We can use
		 * array_slice() to
		 */
		$data = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		/**
		 * REQUIRED. Now we can add our *sorted* data to the items property, where
		 * it can be used by the rest of the class.
		 */
		$this->items = $data;

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->set_pagination_args(array( 'total_items' => $total_items,
           'per_page'    => $per_page,
           'total_pages' => ceil($total_items / $per_page)
       ));
	}
}