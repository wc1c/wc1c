<?php
/**
 * Singleton trait
 *
 * @package Wc1c
 */
defined('ABSPATH') || exit;

trait Trait_Wc1c_Singleton
{
	/**
	 * All initialized instances
	 *
	 * @var array
	 */
	private static $instances = [];

	/**
	 * @return self
	 */
	public static function instance()
	{
		if(!isset(self::$instances[static::class]))
		{
			self::$instances[static::class] = new static;
		}

		return self::$instances[static::class];
	}
}