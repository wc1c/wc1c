<?php
/**
 * Namespace
 */
namespace Wc1c\Admin\Extensions;

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
		$extensions = wc1c()->getExtensions();

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