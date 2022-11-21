<?php namespace Digiom\Woplucore;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Interfaces\Activable;
use Digiom\Woplucore\Traits\SingletonTrait;

/**
 * Activation
 *
 * @package Digiom\Woplucore
 */
class Activation implements Activable
{
	use SingletonTrait;
}