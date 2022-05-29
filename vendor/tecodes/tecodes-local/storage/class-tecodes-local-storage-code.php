<?php
/**
 * Tecodes local code storage class
 *
 * @package Tecodes/Local
 */
class Tecodes_Local_Storage_Code implements Interface_Tecodes_Local_Storage_Code
{
	/**
	 * @var string
	 */
	protected $option_name = '';

	/**
	 * @param $local_code
	 *
	 * @return bool
	 */
	public function update($local_code)
	{
		if(!function_exists('update_option'))
		{
			$fp = @fopen('./tecodes.code', 'w');
			if(!$fp)
			{
				return false;
			}
			fwrite($fp, $local_code);
			fclose($fp);
			return true;
		}

		return update_option($this->option_name, $local_code, 'no');
	}

	/**
	 * @return bool|string
	 */
	public function read()
	{
		if(!function_exists('get_option'))
		{
			$local_key = @file_get_contents('./tecodes.code');

			if($local_key)
			{
				return $local_key;
			}

			return false;
		}

		return get_option($this->option_name);
	}

	/**
	 * @return bool
	 */
	public function delete()
	{
		if(!function_exists('delete_option'))
		{
			$fp = @fopen('./tecodes.code', 'w');
			if(!$fp)
			{
				return false;
			}
			fwrite($fp, '');
			fclose($fp);
			return true;
		}

		return delete_option($this->option_name);
	}
}