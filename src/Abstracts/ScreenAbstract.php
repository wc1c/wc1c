<?php
/**
 * Namespace
 */
namespace Wc1c\Abstracts;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * ScreenAbstract
 *
 * @package Wc1c\Abstracts
 */
abstract class ScreenAbstract
{
	/**
	 * ScreenAbstract constructor.
	 */
	public function __construct()
	{
		add_action('wc1c_admin_show', [$this, 'output'], 10);
	}

	/**
	 * @return mixed
	 */
	abstract public function output();
}