<?php
/**
 * Namespace
 */
namespace Wc1c\Tools;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Exceptions\Exception;
use Wc1c\Traits\SingletonTrait;

/**
 * Core
 *
 * @package Wc1c\Tools
 */
final class Core
{
	use SingletonTrait;

	/**
	 * @var array All loaded tools
	 */
	private $tools = [];

	/**
	 * Core constructor.
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->load();
	}

	/**
	 * Loading tools
	 *
	 * @return void
	 * @throws Exception
	 */
	public function load()
	{
		/**
		 * key = tool id
		 * value = callback - ToolAbstract
		 */
		$tools =
		[
			'environments' => Environments\Init::class
		];

		/**
		 * External tools loading is enabled
		 */
		if('yes' === wc1c()->settings()->get('extensions_tools', 'yes'))
		{
			$tools = apply_filters(WC1C_PREFIX . 'load_tools', $tools);
		}

		try
		{
			$this->set($tools);
		}
		catch(Exception $e)
		{
			throw new Exception('exception - ' . $e->getMessage());
		}
	}

	/**
	 * Get tools
	 *
	 * @param string $tool_id
	 *
	 * @return array|mixed
	 * @throws Exception
	 */
	public function get($tool_id = '')
	{
		if('' !== $tool_id)
		{
			if(array_key_exists($tool_id, $this->tools))
			{
				return $this->tools[$tool_id];
			}

			throw new Exception('$schema_id is unavailable');
		}

		return $this->tools;
	}

	/**
	 * Set tools
	 *
	 * @param array $tools
	 *
	 * @return void
	 * @throws Exception
	 */
	public function set($tools)
	{
		if(!is_array($tools))
		{
			throw new Exception('$tools is not valid');
		}

		$this->tools = $tools;
	}

	/**
	 * @param string $tool_id
	 *
	 * @throws Exception
	 */
	public function init($tool_id = '')
	{
		try
		{
			$tool_data = $this->get($tool_id);
			$tool_example = new $tool_data();
		}
		catch(Exception $e)
		{
			throw new Exception('exception - ' . $e->getMessage());
		}

		$tool_example->set_id('example');
		$tool_example->set_name(__('Example tool', 'wc1c'));
		$tool_example->set_description(__('A demo tool that does not carry any functional load.', 'wc1c'));

		$tools[$tool_example->get_id()] = $tool_example;
	}
}