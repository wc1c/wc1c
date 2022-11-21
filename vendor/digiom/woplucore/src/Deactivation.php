<?php namespace Digiom\Woplucore;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Interfaces\Deactivable;
use Digiom\Woplucore\Traits\SingletonTrait;

/**
 * Deactivation
 *
 * @package Digiom\Woplucore
 */
class Deactivation implements Deactivable
{
	use SingletonTrait;
}