<?php
/**
 * Timer class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Timer
{
	/**
	 * @var int
	 */
	private $maximum = 30;

	/**
	 * @var int
	 */
	private $started = 0;

	/**
	 * Wc1c_Timer constructor
	 */
	public function __construct()
	{
		if(isset($_SERVER['REQUEST_TIME_FLOAT']))
		{
			$this->set_started($_SERVER['REQUEST_TIME_FLOAT']);
		}
		else
		{
			$this->set_started(time());
		}
	}

	/**
	 * @return int
	 */
	public function get_maximum()
	{
		return $this->maximum;
	}

	/**
	 * @param int $maximum
	 */
	public function set_maximum($maximum)
	{
		$this->maximum = $maximum;
	}

	/**
	 * @return int
	 */
	public function get_started()
	{
		return $this->started;
	}

	/**
	 * @param int $started
	 */
	public function set_started($started)
	{
		$this->started = $started;
	}

	/**
	 * @return float|int
	 */
	public function get_execution_seconds()
	{
		return microtime(true) - $this->started;
	}

	/**
	 * @return float|int
	 */
	public function get_available_seconds()
	{
		return $this->maximum - $this->get_execution_seconds();
	}
}