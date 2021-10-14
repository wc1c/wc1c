<?php
/**
 * Wc1c interface
 *
 * @package Wc1c/Interfaces
 */
defined('ABSPATH') || exit;

interface Interface_Wc1c
{
	/**
	 * Global timer
	 *
	 * @return Wc1c_Timer
	 */
	public function timer();

	/**
	 * Global settings
	 *
	 * @return Wc1c_Settings
	 */
	public function settings();

	/**
	 * Initialize
	 *
	 * @return mixed
	 */
	public function init();
}