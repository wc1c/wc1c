<?php namespace Wc1c\Admin\Configurations;

defined('ABSPATH') || exit;

use Wc1c\Abstracts\ScreenAbstract;
use Wc1c\Traits\SingletonTrait;

/**
 * All
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