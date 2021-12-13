<?php
/**
 * Namespace
 */
namespace Wc1c\Exceptions;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use RuntimeException as SystemRuntimeException;

/**
 * RuntimeException
 *
 * @package Wc1c/Exceptions
 */
class RuntimeException extends SystemRuntimeException
{}