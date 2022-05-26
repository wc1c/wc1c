<?php namespace Wc1c\Wc\Contracts;

defined('ABSPATH') || exit;

/**
 * ImagesStorageContract
 *
 * @package Wc1c\Wc
 */
interface ImagesStorageContract
{
	/**
	 * Получение изображения или изображений по наименованию изображения
	 *
	 * @param string $name Наименование изображения
	 *
	 * @return false|ImageContract|ImageContract[]
	 */
	public function getByName($name);

	/**
	 * Получение изображения или изображений по наименованию изображения в 1С
	 *
	 * @param string $name Наименование изображения
	 *
	 * @return false|ImageContract|ImageContract[]
	 */
	public function getByExternalName($name);
}