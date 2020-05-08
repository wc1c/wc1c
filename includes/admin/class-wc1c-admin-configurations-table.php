<?php
/**
 * Admin configurations table class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Configurations_Table extends Wc1c_Admin_Abstract_Table
{
	/**
	 * Wc1c_Admin_Configurations_Table constructor
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
		esc_html_e( 'The configuration was not found. Add a new configuration via the menu.', 'wc1c' );
	}

	/**
	 * Get a list of CSS classes for the WP_List_Table table tag
	 *
	 * @return array  - list of CSS classes for the table tag
	 */
	protected function get_table_classes()
	{
		return array('widefat', 'striped', $this->_args['plural']);
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

				$status_return = 'Неизвестный';

				if($item['status'] == 'draft')
				{
					$status_return = '<span class="draft">Черновик</span>';
				}
				if($item['status'] == 'active')
				{
					$status_return = '<span class="active">Работает</span>';
				}
				if($item['status'] == 'inactive')
				{
					$status_return = '<span class="inactive">Остановлена</span>';
				}

				return $status_return;

			case 'date_create':
			case 'date_activity':
			case 'date_modify':
				return sprintf( __( '%s <br/><span class="wc1c-time">Time: %s</span>', 'wc1c' ), date_i18n( 'Y-m-d', strtotime( $item[ $column_name ] ) ), date_i18n( 'H:i:s', strtotime( $item[ $column_name ] ) ) );

			default:
				return print_r( $item, true );
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
			'edit' => sprintf( '<a href="?page=%s&section=configurations&do_action=%s&config_id=%s">' . __( 'Edit', 'wc1c' ) . '</a>', $_REQUEST['page'], 'update', $item['config_id'] ),
			'delete' => sprintf( '<a href="?page=%s&section=configurations&action=%s&config_id=%s">' . __( 'Remove', 'wc1c' ) . '</a>', $_REQUEST['page'], 'remove', $item['config_id'] ),
		);

		//Return the title contents
		return sprintf( '%1$s <br/> %2$s',
			/*$1%s*/
			$item['config_name'],
			/*$2%s*/
			$this->row_actions( $actions, true )
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
		/**
		 * Default actions
		 */
	    $actions['remove'] = __('Remove', 'wc1c');

		/**
		 * Load actions from external code
		 */
		$actions = apply_filters('wc1c_admin_configuration_get_table_bulk_actions', $actions);

		return $actions;
	}

	/**
	 * All columns
	 *
	 * @return array
	 */
	public function get_columns()
	{
		$columns = [];
		$columns['cb'] = '<input type="checkbox" />';
		$columns['config_id'] = __('ID', 'wc1c');
		$columns['config_name'] = __('Name', 'wc1c');
		$columns['status'] = __('Status', 'wc1c');
		$columns['schema'] = __('Schema ID', 'wc1c');
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
		$sortable_columns['config_id'] = array('config_id', true);
		$sortable_columns['status'] = array('status', false);

		return $sortable_columns;
	}

	/**
	 * Handles the checkbox column output
	 *
	 * @param WP_Post $item The current WP_Post object.
	 */
	public function column_cb($item)
	{
		?>
		<label class="screen-reader-text" for="cb-select-<?php echo $item['config_id']; ?>">
			<?php
			printf(__( 'Select %s' ), $item['config_name']);
			?>
		</label>
		<input type="checkbox" name="config_id[]" id="cb-select-<?php echo $item['config_id']; ?>" value="<?php echo $item['config_id']; ?>" />
		<?php
	}

	/**
	 * Actions
	 */
	public function process_bulk_action()
	{
		/**
		 * Remove configuration
		 */
		if('remove' == $this->current_action())
		{
		    $configs = [];

		    if(isset($_REQUEST['config_id']))
            {
            	if(is_array($_POST['config_id']))
	            {
	            	$configs = array_merge($configs, $_REQUEST['config_id']);
	            }
            	else
	            {
		            $configs = array($_GET['config_id']);
	            }

	            foreach($configs as $config_id)
	            {
		            WC1C_Db()->delete(WC1C_Db()->prefix . "wc1c", array('config_id' => $config_id));
	            }

	            echo WC1C_Admin()->format_message('update', __('Config deleted', 'wc1c'));
            }
		    else
		    {
			    echo WC1C_Admin()->format_message('error', __('Configuration ID not found!', 'wc1c'));
		    }
		}
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
		 * Optional. You can handle your bulk actions however you see fit. In this
		 * case, we'll handle them within our package just to keep things clean.
		 */
		$this->process_bulk_action();

		/**
		 * Instead of querying a database, we're going to fetch the example data
		 * property we created for use in this plugin. This makes this example
		 * package slightly different than one you might build on your own. In
		 * this example, we'll be using array manipulation to sort and paginate
		 * our data. In a real-world implementation, you will probably want to
		 * use sort and pagination data to build a custom query instead, as you'll
		 * be able to use your precisely-queried data immediately.
		 */
		$data = WC1C_Db()->get_results( "SELECT * FROM " . WC1C_Db()->prefix . "wc1c", ARRAY_A );

		/**
		 * This checks for sorting input and sorts the data in our array accordingly.
		 *
		 * In a real-world situation involving a database, you would probably want
		 * to handle sorting by passing the 'orderby' and 'order' values directly
		 * to a custom query. The returned data will be pre-sorted, and this array
		 * sorting technique would be unnecessary.
		 */
		function usort_reorder( $a, $b )
		{
			$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'config_id';
			$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'desc';

			$result  = strcmp( $a[ $orderby ], $b[ $orderby ] );

			return ( $order === 'asc' ) ? $result : - $result;
		}

		usort( $data, 'usort_reorder' );

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
		$total_items = count( $data );

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