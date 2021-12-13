<?php
/**
 * Namespace
 */
namespace Wc1c;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Class Timer
 *
 * @package Wc1c
 */
class Timer
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
	 * Timer constructor.
	 */
	public function __construct()
	{
		if(isset($_SERVER['REQUEST_TIME_FLOAT']))
		{
			$this->setStarted($_SERVER['REQUEST_TIME_FLOAT']);
		}
	}

	/**
	 * Available maximum secs
	 *
	 * @return int
	 */
	public function getMaximum()
	{
		return $this->maximum;
	}

	/**
	 * Set maximum available secs
	 *
	 * @param int $maximum
	 */
	public function setMaximum($maximum)
	{
		$this->maximum = $maximum;
	}

	/**
	 * Timer started
	 *
	 * @return int
	 */
	public function getStarted()
	{
		return $this->started;
	}

	/**
	 * Set timer started
	 *
	 * @param int $started
	 */
	public function setStarted($started)
	{
		$this->started = $started;
	}

	/**
	 * Current executions time in secs
	 *
	 * @return float|int
	 */
	public function getExecutionSeconds()
	{
		return microtime(true) - $this->getStarted();
	}

	/**
	 * Available secs for executions
	 *
	 * @return float|int
	 */
	public function getAvailableSeconds()
	{
		return $this->getMaximum() - $this->getExecutionSeconds();
	}

	/**
	 * Get available seconds bigger than secs
	 *
	 * @param int $seconds
	 *
	 * @return bool
	 */
	public function isRemainingBiggerThan($seconds = 30)
	{
		$max_execution_time = $this->getMaximum();

		if($max_execution_time === 0)
		{
			return true;
		}

		$remaining_seconds = $this->getAvailableSeconds();

		return ($remaining_seconds >= $seconds);
	}
}