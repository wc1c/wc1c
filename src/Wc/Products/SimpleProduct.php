<?php namespace Wc1c\Wc\Products;

defined('ABSPATH') || exit;

use WC_Product_Simple;
use Wc1c\Exceptions\Exception;
use Wc1c\Wc\Contracts\ProductContract;
use Wc1c\Wc\Traits\Cases;

/**
 * SimpleProduct
 *
 * @package Wc1c\Wc
 */
class SimpleProduct extends WC_Product_Simple implements ProductContract
{
	use Cases;

	/**
	 * Получение идентификатора продукта
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->get_id();
	}

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

	/**
	 * @param $id
	 *
	 * @return void
	 */
	public function setConfigurationId($id)
	{
		$this->add_meta_data('_wc1c_configuration_id', $id, true);
	}

	/**
	 * @param $context
	 *
	 * @return array|string
	 */
	public function getConfigurationId($context = 'view')
	{
		return $this->get_meta('_wc1c_configuration_id', true, $context);
	}

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

	/**
	 * Установка артикула продукта с опциональным учетом уникальности
	 *
	 * @param string $sku Артикул продукта
	 * @param boolean $unique Требовать ли уникальное значение
	 *
	 * @return void
	 * @throws Exception
	 */
	public function setSku($sku, $unique = false)
	{
		$sku = (string)$sku;

		if(false === $unique)
		{
			$this->set_prop('sku', $sku);
		}

		try
		{
			$this->set_sku($sku);
		}
		catch(\Exception $e)
		{
			throw new Exception($e->getMessage());
		}
	}

	/**
	 * Получение текущего артикула продукта
	 *
	 * @param string $context Контекст запроса
	 *
	 * @return string
	 */
	public function getSku($context = 'view')
	{
		return $this->get_sku();
	}

	/**
	 * Checks the product type.
	 *
	 * @param string|array $type Array or string of types.
	 *
	 * @return bool
	 */
	public function isType($type)
	{
		return $this->is_type($type);
	}
}