<?php namespace Digiom\Woplucore;

defined('ABSPATH') || exit;

use Digiom\Woplucore\Interfaces\Uninstallable;
use Digiom\Woplucore\Traits\SingletonTrait;

/**
 * Uninstall
 *
 * @package Digiom\Woplucore
 */
class Uninstall implements Uninstallable
{
	use SingletonTrait;
}