<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Datetime as SystemDatetime;
use DateTimeZone;

/**
 * Datetime
 *
 * @package Wc1c
 */
class Datetime extends SystemDatetime
{
	/**
	 * UTC Offset, if needed. Only used when a timezone is not set. When timezones are used this will equal 0
	 *
	 * @var integer
	 */
	protected $utc_offset = 0;

	/**
	 * Output an ISO 8601 date string in local (WordPress) timezone
	 *
	 * @return string
	 */
	public function __toString()
	{
		return $this->format(DATE_ATOM);
	}

	/**
	 * Set UTC offset - this is a fixed offset instead of a timezone
	 *
	 * @param int $offset Offset
	 */
	public function setUtcOffset($offset)
	{
		$this->utc_offset = (int) $offset;
	}

	/**
	 * Get UTC offset if set, or default to the DateTime object's offset
	 */
	public function getOffset()
	{
		return $this->utc_offset ?: parent::getOffset();
	}

	/**
	 * Set timezone
	 *
	 * @param DateTimeZone $timezone DateTimeZone instance
	 *
	 * @return DateTime
	 */
	public function setTimezone($timezone)
	{
		$this->utc_offset = 0;

		return parent::setTimezone($timezone);
	}

	/**
	 * Missing in PHP 5.2 so just here, so it can be supported consistently
	 *
	 * @return int
	 */
	public function getTimestamp()
	{
		return method_exists(SystemDatetime::class, 'getTimestamp') ? parent::getTimestamp() : $this->format('U');
	}

	/**
	 * Get the timestamp with the WordPress timezone offset added or subtracted
	 *
	 * @return int
	 */
	public function getOffsetTimestamp()
	{
		return $this->getTimestamp() + $this->getOffset();
	}

	/**
	 * Format a date based on the offset timestamp
	 *
	 * @param string $format Date format.
	 *
	 * @return string
	 */
	public function date($format)
	{
		return gmdate($format, $this->getOffsetTimestamp());
	}

	/**
	 * Return a localised date based on offset timestamp. Wrapper for date_i18n function
	 *
	 * @param string $format Date format
	 *
	 * @return string
	 */
	public function dateI18N($format = 'Y-m-d')
	{
		return date_i18n($format, $this->getOffsetTimestamp());
	}
}