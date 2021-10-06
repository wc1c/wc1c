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
	 * Maximum time is available in sec
	 *
	 * @var int
	 */
	private $maximum = 30;

	/**
	 * Timer started
	 *
	 * @var int|float
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
	}

	/**
	 * Available maximum secs
	 *
	 * @return int
	 */
	public function get_maximum()
	{
		return $this->maximum;
	}

	/**
	 * Set maximum available secs
	 *
	 * @param int $maximum
	 */
	public function set_maximum($maximum)
	{
		$this->maximum = $maximum;
	}

	/**
	 * Timer started
	 *
	 * @return int
	 */
	public function get_started()
	{
		return $this->started;
	}

	/**
	 * Set timer started
	 *
	 * @param int $started
	 */
	public function set_started($started)
	{
		$this->started = $started;
	}

	/**
	 * Current executions time in secs
	 *
	 * @return float|int
	 */
	public function get_execution_seconds()
	{
		return microtime(true) - $this->get_started();
	}

	/**
	 * Available secs for executions
	 *
	 * @return float|int
	 */
	public function get_available_seconds()
	{
		return $this->get_maximum() - $this->get_execution_seconds();
	}

	/**
	 * Get available seconds bigger than secs
	 *
	 * @param int $seconds
	 *
	 * @return bool
	 */
	public function is_remaining_bigger_than($seconds = 30)
	{
		$max_execution_time = $this->get_maximum();

		if($max_execution_time === 0)
		{
			return true;
		}

		$remaining_seconds = $this->get_available_seconds();

		return ($remaining_seconds >= $seconds);
	}
}