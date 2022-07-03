<?php namespace Wc1c\Wc\Products\Traits;

defined('ABSPATH') || exit;

/**
 * SchemaProductTrait
 *
 * @package Wc1c\Wc
 */
trait SchemaProductTrait
{
	/**
	 * Установка идентификатора схемы
	 *
	 * @param string|int $id Идентификатор схемы
	 *
	 * @return void
	 */
	public function setSchemaId($id)
	{
		$this->add_meta_data('_wc1c_schema_id', $id, true);
	}

	/**
	 * @param $context
	 *
	 * @return array|string
	 */
	public function getSchemaId($context = 'view')
	{
		return $this->get_meta('_wc1c_schema_id', true, $context);
	}
}