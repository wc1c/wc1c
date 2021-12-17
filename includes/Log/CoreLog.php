<?php
/**
 * Namespace
 */
namespace Wc1c\Log;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use Monolog\Logger;

/**
 * Class CoreLog
 *
 * @package Wc1c
 */
final class CoreLog extends Logger
{
	/**
	 * @var string
	 */
	protected $name = 'core';

	/**
	 * CoreLog constructor.
	 *
	 * @param array $handlers
	 * @param array $processors
	 */
	public function __construct(array $handlers = [], array $processors = [])
	{
		parent::__construct($this->name, $handlers, $processors);
	}
}