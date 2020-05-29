<?php
/**
 * Schema logger class
 *
 * @package Wc1c/Schemas
 */
defined('ABSPATH') || exit;

class Wc1c_Schema_Logger extends Wc1c_Abstract_Logger
{
	/**
	 * Save to file
	 *
	 * @param $level
	 * @param $message
	 * @param null $object
	 *
	 * @return bool
	 * @throws Exception
	 */
	public function add($level, $message, $object = null)
	{
		if($this->get_level() > $level)
		{
			return false;
		}

		try
		{
			$date_time = new DateTime('now', new DateTimeZone('UTC'));
		}
		catch(Exception $e)
		{
			return false;
		}

		$content = array
		(
			$level,
			$date_time->format(DATE_ATOM),
			$this->levels[$level],
			$message
		);

		if(is_object($object) || is_array($object))
		{
			$content['object'] = print_r($object, true);
		}
		elseif(is_bool($object))
		{
			$content['object'] = $object ? 'true' : 'false';
		}
		elseif(!is_null($object) && $object !== '')
		{
			$content['object'] = $object;
		}

		$content = implode(' |- ', $content);

		$file = $this->get_path() . DIRECTORY_SEPARATOR . $this->get_name();

		if(!file_exists($this->get_path()))
		{
			mkdir($this->get_path());
		}

		file_put_contents
		(
			$file,
			$content . PHP_EOL,
			FILE_APPEND | LOCK_EX
		);

		return true;
	}
}