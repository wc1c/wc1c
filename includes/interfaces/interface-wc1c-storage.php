<?php
/**
 * Storage
 *
 * @package Wc1c/Interfaces
 */
defined('ABSPATH') || exit;

interface Interface_Wc1c_Storage
{
	/**
	 * Method to create a new record
	 * of a Abstract_Wc1c_Data based object
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 */
	public function create(&$data);

	/**
	 * Method to read a record.
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 */
	public function read(&$data);

	/**
	 * Updates a record in the database
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 */
	public function update(&$data);

	/**
	 * Deletes a record from the database
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 * @param array $args Array of args to pass to the delete method
	 *
	 * @return bool result
	 */
	public function delete(&$data, $args = []);
}