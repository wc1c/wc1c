<?php
/**
 * Abstract Logger class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

abstract class Abstract_Wc1c_Logger
{
	/**
	 * Default level
	 *
	 * @var int
	 */
	public $level = 400;

	/**
	 * Log name
	 *
	 * @var string
	 */
	private $name = 'wc1c.boot.log';

    /**
     * Logging levels (RFC 5424)
     *
     * @var array
     */
    public $levels =
    [
        100 => 'DEBUG',
        200 => 'INFO',
        250 => 'NOTICE',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    ];

	/**
	 * Wc1c_Abstract_Logger constructor
	 *
	 * @param int $level
	 * @param string $name
	 *
	 * @throws Exception
	 */
    public function __construct($level = 400, $name = '')
    {
	    if($name !== '')
	    {
		    $this->set_name($name);
	    }

        if($level !== '')
        {
            $this->level = $level;
        }
    }

	/**
	 * Get log name
	 *
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * Set log name
	 *
	 * @param string $name
	 */
	public function set_name($name)
	{
		$this->name = $name;
	}

	/**
	 * Get log level
	 *
	 * @return int
	 */
	public function get_level()
	{
		return $this->level;
	}

	/**
	 * Set log level
	 *
	 * @param int $level
	 */
	public function set_level($level)
	{
		$this->level = $level;
	}

    /**
     * Level: warning
     *
     * @param $message
     */
    public function warning($message)
    {
        $this->write(300, $message);
    }

    /**
     * Level: error
     *
     * @param $message
     * @param null $object
     */
    public function error($message, $object = null)
    {
        $this->write(400, $message, $object);
    }

    /**
     * Level: debug
     *
     * @param $message
     * @param null $object
     */
    public function debug($message, $object = null)
    {
        $this->write(100, $message, $object);
    }

    /**
     * Level: info
     *
     * @param $message
     */
    public function info($message)
    {
        $this->write(200, $message);
    }

    /**
     * Level: notice
     *
     * @param $message
     */
    public function notice($message)
    {
        $this->write(250, $message);
    }

    /**
     * Level: critical
     *
     * @param $message
     * @param null $object
     */
    public function critical($message, $object = null)
    {
        $this->write(500, $message, $object);
    }

    /**
     * Level: alert
     *
     * @param $message
     * @param null $object
     */
    public function alert($message, $object = null)
    {
        $this->write(550, $message, $object);
    }

    /**
     * Level: emergency
     *
     * @param $message
     * @param null $object
     */
    public function emergency($message, $object = null)
    {
        $this->write(600, $message, $object);
    }

	/**
	 * Initialize
	 *
	 * @return mixed
	 */
	abstract public function init();

    /**
     * Add
     *
     * @param $level
     * @param $message
     * @param null $object
     */
	abstract public function write($level, $message, $object = null);
}