<?php namespace Wc1c\Wc\Products\Traits;

defined('ABSPATH') || exit;

/**
 * ExternalProductTrait
 *
 * @package Wc1c\Wc
 */
trait ExternalProductTrait
{
	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function setExternalId($id)
	{
		$this->add_meta_data('_wc1c_external_id', $id, true);
	}

	/**
	 * @param $context
	 *
	 * @return array|string
	 */
	public function getExternalId($context = 'view')
	{
		return $this->get_meta('_wc1c_external_id', true, $context);
	}

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function setExternalCharacteristicId($id)
	{
		$this->add_meta_data('_wc1c_external_characteristic_id', $id, true);
	}

	/**
	 * @param $context
	 *
	 * @return array|string
	 */
	public function getExternalCharacteristicId($context = 'view')
	{
		return $this->get_meta('_wc1c_external_characteristic_id', true, $context);
	}
}