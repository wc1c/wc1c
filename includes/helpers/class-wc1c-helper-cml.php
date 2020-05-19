<?php
/**
 * Helper class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Helper_Cml
{
	/**
	 * Wc1c_Helper_Cml constructor
	 */
	public function __construct()
	{
	}

	/**
	 * Определение типа файла
	 *
	 * @param $file_name
	 *
	 * @return string
	 */
	public function file_type_detect($file_name)
	{
		$types = array('import', 'offers', 'prices', 'rests', 'import_files');
		foreach($types as $type)
		{
			$pos = stripos($file_name, $type);
			if($pos !== false)
			{
				return $type;
			}
		}
		return '';
	}
}