<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Configurations;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Wc1c\Abstracts\ScreenAbstract;
use Wc1c\Traits\SingletonTrait;

/**
 * Class Lists
 *
 * @package Wc1c\Admin\Configurations
 */
class All extends ScreenAbstract
{
	use SingletonTrait;

	/**
	 * Build and output table
	 */
	public function output()
	{
		$list_table = new AllTable();
		$list_table->prepareItems();
		$list_table->display();
	}
}