<?php namespace Digiom\Psr7wp\Exceptions;

defined('ABSPATH') || exit;

use InvalidArgumentException;

/**
 * Class MalformedUriException
 * Exception thrown if a URI cannot be parsed because it's malformed.
 *
 * @package Digiom\Psr7wp
 */
class MalformedUriException extends InvalidArgumentException
{
}
