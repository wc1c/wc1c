<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Digiom\ApClientWP\ApplicationsPasswords;

/**
 * Connection
 *
 * @package Wc1c
 */
final class Connection extends ApplicationsPasswords
{
	/**
	 * @var string
	 */
	private $host = 'https://wc1c.info';
}