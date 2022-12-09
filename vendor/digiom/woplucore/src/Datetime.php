<?php namespace Digiom\Woplucore;

defined('ABSPATH') || exit;

use DateTimeZone;

/**
 * Datetime
 *
 * @package Digiom\Woplucore
 */
class Datetime extends \Datetime
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
	public function setUtcOffset(int $offset)
	{
		$this->utc_offset = (int) $offset;
	}

	/**
	 * Get UTC offset if set, or default to the DateTime object's offset
	 */
	public function getOffset(): int
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
	public function setTimezone($timezone): Datetime
	{
		$this->utc_offset = 0;

		return parent::setTimezone($timezone);
	}

	/**
	 * Get the timestamp with the WordPress timezone offset added or subtracted
	 *
	 * @return int
	 */
	public function getOffsetTimestamp(): int
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
	public function date(string $format): string
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
	public function dateI18N(string $format = 'Y-m-d'): string
	{
		return date_i18n($format, $this->getOffsetTimestamp());
	}
}