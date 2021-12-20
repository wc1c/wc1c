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
			wc1c()->templates()->getTemplate('extensions/empty.php');
			return;
		}

		foreach($extensions as $extension_id => $extension_object)
		{
			$args =
			[
				'id' => $extension_id,
				'object' => $extension_object
			];

			wc1c()->templates()->getTemplate('extensions/item.php', $args);
		}
	}
}