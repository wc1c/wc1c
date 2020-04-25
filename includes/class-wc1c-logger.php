<?php
/**
 * Logger class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

class Wc1c_Logger extends Wc1c_Abstract_Logger
{
    /**
     * Save to file
     *
     * @throws
     *
     * @param $level
     * @param $message
     * @param null $object
     *
     * @return bool
     */
    public function add($level, $message, $object = null)
    {
        /**
         * Check level
         */
        if($this->get_level() > $level)
        {
            return false;
        }

	    try
	    {
		    $this->date_time = new DateTime('now', new DateTimeZone('UTC'));
	    }
        catch(Exception $e)
        {}

        $content = array
        (
            $level,
            $this->date_time->format(DATE_ATOM),
            $this->levels[$level],
            $message
        );

        if(is_object($object) || is_array($object))
        {
            $content['object'] = print_r($object, true);
        }
	    else
	    {
		    $content['object'] = $object;
	    }

        /**
         * Content
         */
        $content = implode(' -|- ', $content);

	    /**
	     * File
	     */
	    $file = $this->get_path() . DIRECTORY_SEPARATOR . $this->get_name();

	    /**
	     * Dir
	     */
        if(!file_exists($this->get_path()))
        {
        	mkdir($this->get_path()); // todo: move to change with settings
        }

	    /**
	     * Write
	     */
        file_put_contents
        (
	        $file,
            $content . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );

        return true;
    }
}