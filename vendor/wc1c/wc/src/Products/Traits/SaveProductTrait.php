<?php namespace Wc1c\Wc\Products\Traits;

defined('ABSPATH') || exit;

/**
 * SaveProductTrait
 *
 * @package Wc1c\Wc
 */
trait SaveProductTrait
{
	/**
	 * Save data (either create or update depending on if we are working on an existing product).
	 *
	 * @return int
	 */
	public function save()
	{
		$wc1c_version = wc1c()->environment()->get('wc1c_version');

		if(false === $this->exists())
		{
			$this->add_meta_data('_wc1c_version_init', $wc1c_version, true);
		}

		$this->add_meta_data('_wc1c_version', $wc1c_version, true);

		return parent::save();
	}
}