<?php
/**
 * Namespace
 */
namespace Wc1c\Admin;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Abstracts\ScreenAbstract;
use Wc1c\Exceptions\Exception;
use Wc1c\Exceptions\RuntimeException;
use Wc1c\Traits\SingletonTrait;

/**
 * Class Tools
 *
 * @package Wc1c\Admin
 */
final class Tools extends ScreenAbstract
{
	use SingletonTrait;

	/**
	 * @var array All available tools
	 */
	private $tools = [];

	/**
	 * @var string Current tool id
	 */
	private $current_tool_id = '';

	/**
	 * Tools constructor.
	 */
	public function __construct()
	{
		$this->init();
		parent::__construct();
	}

	/**
	 * Initialized
	 *
	 * @throws RuntimeException
	 */
	public function init()
	{
		try
		{
			$tools = wc1c()->tools()->get();
			$this->tools = $tools;
		}
		catch(Exception $exception){}

		$this->initCurrentId();
	}

	/**
	 * @return bool
	 */
	protected function initCurrentId()
	{
		$tool_id = wc1c_get_var($_GET['tool_id'], '');

		if(!empty($tool_id) && array_key_exists($tool_id, $this->tools))
		{
			$this->setCurrentToolId($tool_id);
			return true;
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getCurrentToolId()
	{
		return $this->current_tool_id;
	}

	/**
	 * @param string $current_tool_id
	 */
	public function setCurrentToolId($current_tool_id)
	{
		$this->current_tool_id = $current_tool_id;
	}

	/**
	 * Output tools table
	 *
	 * @return void
	 */
	public function output()
	{
		if(empty($this->tools))
		{
			wc1c_get_template('tools/empty.php');
			return;
		}

		if($this->getCurrentToolId() !== '' && is_wc1c_admin_tools_request())
		{
			$args =
			[
				'id' => $this->getCurrentToolId(),
				'object' => new $this->tools[$this->getCurrentToolId()]()
			];

			wc1c_get_template('tools/single.php', $args);
		}
		else
		{
			foreach($this->tools as $tool_id => $tool_object)
			{
				if(!class_exists($tool_object))
				{
					continue;
				}

				$args =
				[
					'id' => $tool_id,
					'object' => new $tool_object(),
				];

				wc1c_get_template('tools/item.php', $args);
			}
		}

		wc1c_get_template('tools/all.php');
	}
}