<?php
/**
 * Namespace
 */
namespace Digiom\Psr7wp\Exceptions;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
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
