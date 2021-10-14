<?php
/**
 * Admin tools class
 *
 * @package Wc1c/Admin
 */
defined('ABSPATH') || exit;

class Wc1c_Admin_Tools
{
	/**
	 * Singleton
	 */
	use Trait_Wc1c_Singleton;

	/**
	 * Available tools
	 *
	 * @var array
	 */
	private $tools = [];

	/**
	 * Current tool id
	 *
	 * @var string
	 */
	private $current_tool_id = '';

	/**
	 * Wc1c_Admin_Tools constructor.
	 */
	public function __construct()
	{
		$this->init();
	}

	/**
	 * Initialized
	 *
	 * @throws Wc1c_Exception_Runtime
	 */
	public function init()
	{
		try
		{
			$tools = WC1C()->get_tools();
			$this->set_tools($tools);
		}
		catch(Wc1c_Exception_Runtime $e)
		{
			throw new Wc1c_Exception_Runtime('init: exception -' . $e->getMessage());
		}

		$this->init_current_id();

		/**
		 * Output tools
		 */
		add_action('wc1c_admin_tools_show', [$this, 'output'], 10);
	}

	/**
	 * @return bool
	 */
	protected function init_current_id()
	{
		$tool_id = wc1c_get_var($_GET['tool_id'], '');

		if(!empty($tool_id) && array_key_exists($tool_id, $this->get_tools()))
		{
			$this->set_current_tool_id($tool_id);
			return true;
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function get_current_tool_id()
	{
		return $this->current_tool_id;
	}

	/**
	 * @param string $current_tool_id
	 */
	public function set_current_tool_id($current_tool_id)
	{
		$this->current_tool_id = $current_tool_id;
	}

	/**
	 * @return array
	 */
	public function get_tools()
	{
		return $this->tools;
	}

	/**
	 * @param array $tools
	 */
	public function set_tools($tools)
	{
		$this->tools = $tools;
	}

	/**
	 * Tools
	 *
	 * @return void
	 */
	public function page_tools()
	{
		wc1c_get_template('page.php');
	}

	/**
	 * Output tools table
	 *
	 * @return void
	 */
	public function output()
	{
		$tools = $this->get_tools();

		if($this->get_current_tool_id() !== '' && is_wc1c_admin_tools_request())
		{
			$args =
			[
				'id' => $this->get_current_tool_id(),
				'object' => new $tools[$this->get_current_tool_id()]()
			];

			wc1c_get_template('tools/single.php', $args);
		}
		else
		{
			foreach($tools as $tool_id => $tool_object)
			{
				$args =
				[
					'id' => $tool_id,
					'object' => new $tool_object()
				];

				wc1c_get_template('tools/item.php', $args);
			}
		}
	}
}