<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Digiom\ApClientWP\ApplicationsPasswords;
use Wc1c\Traits\SingletonTrait;

/**
 * Connection
 *
 * @package Wc1c
 */
final class Connection extends ApplicationsPasswords
{
	use SingletonTrait;
}