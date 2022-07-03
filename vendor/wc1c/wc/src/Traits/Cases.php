<?php namespace Wc1c\Wc\Traits;

defined('ABSPATH') || exit;

/**
 * Cases
 */
trait Cases
{
	/**
	 * @param $name
	 * @param $arguments
	 *
	 * @return mixed|void
	 */
	public function __call($name, $arguments)
	{
		$name = $this->camelToKebabCase($name);

		if(method_exists($this, $name))
		{
			return $this->$name(...$arguments);
		}
	}

	/**
	 * @param $camelCase
	 *
	 * @return array|string|string[]|null
	 */
	public function camelToKebabCase($camelCase)
	{
		$pattern = '/([a-z])([A-Z])/';

		$function = static function($matches)
		{
			return $matches[1] . '_' . strtolower($matches[2]);
		};

		return preg_replace_callback($pattern, $function, $camelCase);
	}
}