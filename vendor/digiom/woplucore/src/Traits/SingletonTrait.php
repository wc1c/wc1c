<?php namespace Digiom\Woplucore\Traits;

/**
 * SingletonTrait
 *
 * @package Digiom\Woplucore\Traits
 */
trait SingletonTrait
{
	/**
	 * @var array All initialized instances
	 */
	private static $instances = [];

	/**
	 * Get instance
	 *
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