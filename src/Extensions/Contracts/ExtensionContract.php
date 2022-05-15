<?php namespace Wc1c\Extensions\Contracts;

defined('ABSPATH') || exit;

use Wc1c\Exceptions\Exception;

/**
 * ExtensionContract
 *
 * @package Wc1c\Extenstions
 */
interface ExtensionContract
{
	/**
	 * Initializing
	 *
	 * @return void
	 * @throws Exception
	 */
	public function init();
}