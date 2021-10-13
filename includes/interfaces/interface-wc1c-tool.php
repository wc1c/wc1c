<?php
/**
 * Tool
 *
 * @package Wc1c/Interfaces
 */
defined('ABSPATH') || exit;

interface Interface_Wc1c_Tool
{
	/**
	 * Get tool unique id
	 *
	 * @return string
	 */
	public function get_id();

	/**
	 * Get tool name
	 *
	 * @return string
	 */
	public function get_name();

	/**
	 * Get tool description
	 *
	 * @return string
	 */
	public function get_description();

	/**
	 * Initialize
	 *
	 * @return mixed
	 */
	public function init();

	/**
	 * Output interface
	 *
	 * @return mixed
	 */
	public function output();
}