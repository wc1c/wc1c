<?php namespace Wc1c\Log;

defined('ABSPATH') || exit;

use Monolog\Logger as Monolog;

/**
 * Logger
 *
 * @package Wc1c
 */
final class Logger extends Monolog
{
	/**
	 * @var string
	 */
	protected $name = 'main';
}