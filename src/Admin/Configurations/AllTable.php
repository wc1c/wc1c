<?php namespace Wc1c\Admin\Configurations;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;
use Wc1c\Abstracts\TableAbstract;
use Wc1c\Data\Storage;
use Wc1c\Data\Storages\ConfigurationsStorage;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Traits\ConfigurationsUtilityTrait;
use Wc1c\Traits\DatetimeUtilityTrait;
use Wc1c\Traits\UtilityTrait;

/**
 * AllTable
 *
 * @package Wc1c\Admin\Configurations
 */
class AllTable extends TableAbstract
{
	use ConfigurationsUtilityTrait;
	use DatetimeUtilityTrait;
	use UtilityTrait;

	/**
	 * @var ConfigurationsStorage Configurations storage
	 */
	public $storage_configurations;

	/**
	 * AllTable constructor.
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
			$this->storage_configurations = Storage::load('configuration');
		}
		catch(Exception $e){}

		parent::__construct($params);
	}

	/**
	 * No items found text
	 */
	public function noItems()
	{
		$args['url_create'] = $this->utilityAdminConfigurationsGetUrl('create');

		wc1c()->views()->getView('configurations/empty.php', $args);
	}

	/**
	 * Get a list of CSS classes for the WP_List_Table table tag
	 *
	 * @return array - list of CSS classes for the table tag
	 */
	protected function getTableClasses()
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
	public function columnDefault($item, $column_name)
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
		$timestamp = $this->utilityStringToTimestamp($date) + $this->utilityTimezoneOffset();

		if(!empty($date))
		{
			return sprintf
			(
				__('%s <br/><span class="time">Time: %s</span>', 'wc1c'),
				date_i18n('d/m/Y', $timestamp),
				date_i18n('H:i:s', $timestamp)
			);
		}

		return __('No activity', 'wc1c');
	}

	/**
	 * Account status
	 *
	 * @param $item
	 *
	 * @return string
	 */
	public function column_status($item)
	{
		$status = $this->utilityConfigurationsGetStatusesLabel($item['status']);
		$status_return = $this->utilityConfigurationsGetStatusesLabel('error');

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
			'update' => '<a href="' . $this->utilityAdminConfigurationsGetUrl('update', $item['configuration_id']) . '">' . __('Edit', 'wc1c') . '</a>',
			'delete' => '<a href="' . $this->utilityAdminConfigurationsGetUrl('delete', $item['configuration_id']) . '">' . __('Mark as deleted', 'wc1c') . '</a>',
		];

		if('deleted' === $item['status'] || ('draft' === $item['status'] && 'yes' === wc1c()->settings()->get('configurations_draft_delete', 'yes')))
		{
			unset($actions['update']);
			$actions['delete'] = '<a href="' . $this->utilityAdminConfigurationsGetUrl('delete', $item['configuration_id']) . '">' . __('Remove forever', 'wc1c') . '</a>';
		}

		if('active' === $item['status'])
		{
			unset($actions['delete']);
		}

		$actions = apply_filters('wc1c_admin_configurations_all_row_actions', $actions, $item);

		$user = get_userdata($item['user_id']);
		if($user instanceof \WP_User && $user->exists())
		{
			$metas['user'] = __('User: ', 'wc1c') . $user->get('nickname') . ' (' . $item['user_id']. ')';
		}
		else
		{
			$metas['user'] =  __('User is not exists.', 'wc1c');
		}

		try
		{
			$schema = wc1c()->schemas()->get($item['schema']);
			$metas['schema'] = __('Schema:', 'wc1c') . ' ' . $item['schema'] . ' (' . $schema->getName() . ')';
		}
		catch(RuntimeException $e)
		{
			$metas['schema'] = __('Schema:', 'wc1c') . ' ' . $item['schema'] . ' (' . __('not found, please install the schema', 'wc1c') . ')';
		}

		$metas = apply_filters('wc1c_admin_configurations_all_row_metas', $metas, $item);

		return sprintf
		(
			'<span class="configuration-name">%1$s</span><div class="configuration-metas">%2$s</div><div class="configuration-actions">%3$s</div>',
			$item['name'],
			$this->rowMetas($metas),
			$this->rowActions($actions, true)
		);
	}

	/**
	 * @param $data
	 *
	 * @return string
	 */
	public function rowMetas($data): string
	{
		$metas_count = count($data);

		if(!$metas_count)
		{
			return '';
		}

		$out = '<div class="row-metas">';

		foreach($data as $meta => $meta_text)
		{
			$out .= "<div class='row-metas-line $meta'>$meta_text</div>";
		}

		$out .= '</div>';

		return $out;
	}

	/**
	 * All columns
	 *
	 * @return array
	 */
	public function getColumns(): array
	{
		$columns = [];

		$columns['configuration_id'] = __('ID', 'wc1c');
		$columns['name'] = __('Base information', 'wc1c');
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
	public function getSortableColumns(): array
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
	protected function getDefaultPrimaryColumnName(): string
	{
		return 'configuration_id';
	}

	/**
	 * Creates the different status filter links at the top of the table.
	 *
	 * @return array
	 * @throws Exception
	 */
	protected function getViews(): array
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

		$statuses = $this->utilityConfigurationsGetStatuses();

		foreach($statuses as $status_key)
		{
			$count = $this->storage_configurations->countBy
			(
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
				$this->utilityConfigurationsGetStatusesFolder($status_key),
				$count
			);
		}

		return $status_links;
	}

	/**
	 * Build items
	 */
	public function prepareItems()
	{
		/**
		 * First, lets decide how many records per page to show
		 */
		$per_page = wc1c()->settings()->get('configurations_show_per_page', 10);

		/**
		 * REQUIRED. Now we need to define our column headers. This includes a complete
		 * array of columns to be displayed (slugs & titles), a list of columns
		 * to keep hidden, and a list of columns that are sortable. Each of these
		 * can be defined in another method (as we've done here) before being
		 * used to build the value for our _column_headers property.
		 */
		$columns = $this->getColumns();
		$hidden = [];
		$sortable = $this->getSortableColumns();

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
		$current_page = $this->getPagenum();

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

		$storage_args = [];

		if(array_key_exists('status', $_GET) && in_array($_GET['status'], $this->utilityConfigurationsGetStatuses(), true))
		{
			$storage_args['status'] = $_GET['status'];
		}

		if(!empty($_REQUEST['s']))
		{
			$search_text = wc_clean(wp_unslash($_REQUEST['s']));
			$storage_args['name'] =
			[
				'value' => $search_text,
				'compare_key' => 'LIKE'
			];
		}

		/**
		 * REQUIRED for pagination. Let's check how many items are in our data array.
		 * In real-world use, this would be the total number of items in your database,
		 * without filtering. We'll need this later, so you should always include it
		 * in your own package classes.
		 */
		if(empty($storage_args))
		{
			$total_items = $this->storage_configurations->count();
		}
		else
		{
			$total_items = $this->storage_configurations->countBy($storage_args);
		}

		$storage_args['offset'] = $offset;
		$storage_args['limit'] = $per_page;
		$storage_args['orderby'] = $orderby;
		$storage_args['order'] = $order;

		$this->items = $this->storage_configurations->getData($storage_args, ARRAY_A);

		/**
		 * REQUIRED. We also have to register our pagination options & calculations.
		 */
		$this->setPaginationArgs
		(
			[
				'total_items' => $total_items,
                'per_page' => $per_page,
                'total_pages' => ceil($total_items / $per_page)
            ]
		);
	}

	/**
	 * Extra controls to be displayed between bulk actions and pagination
	 *
	 * @param string $which
	 */
	protected function extraTablenav($which)
	{
		if('top' === $which)
		{
			$this->views();

			$this->searchBox(__('Search', 'wc1c'), 'code');
		}
	}

	/**
	 * Search box
	 *
	 * @param string $text Button text
	 * @param string $input_id Input ID
	 */
	public function searchBox($text, $input_id)
	{
		if(empty($_REQUEST['s']) && !$this->hasItems())
		{
			return;
		}

		$input_id = $input_id . '-search-input';
		$searchQuery = isset($_REQUEST['s']) ? sanitize_text_field(wp_unslash($_REQUEST['s'])) : '';

		echo '<p class="search-box">';
		echo '<label class="screen-reader-text" for="' . esc_attr($input_id) . '">' . esc_html($text) . ':</label>';
		echo '<input type="search" id="' . esc_attr($input_id) . '" name="s" value="' . esc_attr($searchQuery) . '" />';
		submit_button($text, '', '', false, array('id' => 'search-submit'));
		echo '</p>';
	}
}