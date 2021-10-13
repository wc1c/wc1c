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
	 * Pretty times
	 *
	 * @return Wc1c_Timer
	 */
	public function timer();

	/**
	 * Initialize
	 *
	 * @return mixed
	 */
	public function init();
}