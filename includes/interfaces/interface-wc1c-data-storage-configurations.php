<?php
/**
 * Interface data storage: Configurations
 *
 * @package Wc1c/Interfaces
 */
defined('ABSPATH') || exit;

interface Interface_Wc1c_Data_Storage_Configurations
{
	/**
	 * Returns an array of meta for an object
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 *
	 * @return array
	 */
	public function read_meta(&$data);

	/**
	 * Deletes meta based on meta ID
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 * @param object $meta Meta object (containing at least ->id)
	 *
	 * @return array
	 */
	public function delete_meta(&$data, $meta);

	/**
	 * Add new piece of meta.
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 * @param object $meta Meta object (containing ->key and ->value)
	 *
	 * @return int meta ID
	 */
	public function add_meta(&$data, $meta);

	/**
	 * Update meta
	 *
	 * @param Abstract_Wc1c_Data $data Data object
	 * @param object $meta Meta object (containing ->id, ->key and ->value)
	 */
	public function update_meta(&$data, $meta);
}