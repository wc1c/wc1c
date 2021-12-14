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
use Wc1c\Traits\Singleton;

/**
 * Class Lists
 *
 * @package Wc1c\Admin\Configurations
 */
class All extends ScreenAbstract
{
	use Singleton;

	/**
	 * Build and output table
	 */
	public function output()
	{
		$list_table = new AllTable();
		$list_table->prepare_items();
		$list_table->display();
	}
}