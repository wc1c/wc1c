<?php namespace Wc1c\Log;

defined('ABSPATH') || exit;

use Monolog\Logger;

/**
 * ReceiverLog
 *
 * @package Wc1c
 */
class ReceiverLog extends Logger
{
	/**
	 * @var string
	 */
	protected $name = 'receiver';

	/**
	 * ReceiverLog constructor.
	 *
	 * @param array $handlers
	 * @param array $processors
	 */
	public function __construct(array $handlers = [], array $processors = [])
	{
		parent::__construct($this->name, $handlers, $processors);
	}
}