<?php namespace Wc1c\Admin\Extensions;

defined('ABSPATH') || exit;

use Wc1c\Abstracts\ScreenAbstract;
use Wc1c\Traits\SingletonTrait;

/**
 * All
 *
 * @package Wc1c\Admin\Extensions
 */
class All extends ScreenAbstract
{
	use SingletonTrait;

	/**
	 * Build and output table
	 */
	public function output()
	{
		$extensions = wc1c()->extensions()->get();

		if(empty($extensions))
		{
			wc1c()->views()->getView('extensions/empty.php');
			return;
		}

		$args['extensions'] = $extensions;

		wc1c()->views()->getView('extensions/all.php', $args);
	}
}