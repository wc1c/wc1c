<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Digiom\Wap\Client;

/**
 * Connection
 *
 * @package Wc1c
 */
final class Connection extends Client
{
	/**
	 * @var string
	 */
	protected $host = 'https://wc1c.info';
}