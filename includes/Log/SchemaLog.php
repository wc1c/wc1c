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
 * SchemaLog
 *
 * @package Wc1c
 */
class SchemaLog extends Logger
{
	/**
	 * @var string
	 */
	protected $name = 'schema';

	/**
	 * SchemaLog constructor.
	 *
	 * @param array $handlers
	 * @param array $processors
	 */
	public function __construct(array $handlers = [], array $processors = [])
	{
		parent::__construct($this->name, $handlers, $processors);
	}
}