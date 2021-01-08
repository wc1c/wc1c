<?php
/**
 * Abstract Logger class
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

abstract class Wc1c_Abstract_Logger
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
	 * @return string
	 */
	public function get_name()
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function set_name($name)
	{
		$this->name = $name;
	}

	/**
	 * @return int
	 */
	public function get_level()
	{
		return $this->level;
	}

	/**
	 * @param int $level
	 */
	public function set_level($level)
	{
		$this->level = $level;
	}

    /**
     * @param $message
     */
    public function warning($message)
    {
        $this->add(300, $message);
    }

    /**
     * @param $message
     * @param null $object
     */
    public function error($message, $object = null)
    {
        $this->add(400, $message, $object);
    }

    /**
     * @param $message
     * @param null $object
     */
    public function debug($message, $object = null)
    {
        $this->add(100, $message, $object);
    }

    /**
     * @param $message
     */
    public function info($message)
    {
        $this->add(200, $message);
    }

    /**
     * @param $message
     */
    public function notice($message)
    {
        $this->add(250, $message);
    }

    /**
     * @param $message
     * @param null $object
     */
    public function critical($message, $object = null)
    {
        $this->add(500, $message, $object);
    }

    /**
     * @param $message
     * @param null $object
     */
    public function alert($message, $object = null)
    {
        $this->add(550, $message, $object);
    }

    /**
     * @param $message
     * @param null $object
     */
    public function emergency($message, $object = null)
    {
        $this->add(600, $message, $object);
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
	abstract public function add($level, $message, $object = null);
}