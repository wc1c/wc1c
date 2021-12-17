<?php
/**
 * Namespace
 */
namespace Digiom\WordPress\Notices\Interfaces;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Interface StorageInterface
 *
 * @package Digiom\WordPress\Notices\Interfaces
 */
interface ManagerInterface
{
	/**
	 * Adding single notices
	 *
	 * @param string|int $id
	 * @param array $args
	 *
	 * @return boolean
	 */
	public function add($id, $args);

	/**
	 * Get - all or single
	 *
	 * @param $notice_id
	 *
	 * @return mixed
	 */
	public function get($notice_id);

	/**
	 * Cleaning notices
	 *
	 * @return mixed
	 */
	public function purge();

	/**
	 * Create single notices by args
	 *
	 * @param array $args
	 *
	 * @return mixed
	 */
	public function create($args);

	/**
	 * Output notices
	 *
	 * @param $args
	 *
	 * @return mixed
	 */
	public function output($args);
}