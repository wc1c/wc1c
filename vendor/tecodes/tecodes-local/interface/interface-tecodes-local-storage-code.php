<?php
/**
 * Tecodes local storage code interface
 *
 * @package Tecodes/Local
 */
interface Interface_Tecodes_Local_Storage_Code
{
	/**
	 * Установка локального кода
	 *
	 * @param $local_code
	 *
	 * @return bool
	 */
	public function update($local_code);

	/**
	 * Получение локального кода
	 *
	 * @return bool|string
	 */
	public function read();

	/**
	 * Local code delete
	 *
	 * @return bool
	 */
	public function delete();
}