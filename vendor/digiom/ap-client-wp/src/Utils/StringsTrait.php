<?php namespace Digiom\ApClientWP\Utils;

defined('ABSPATH') || exit;

/**
 * StringsTrait
 *
 * @package Digiom\ApClientWP\Utils
 */
trait StringsTrait
{
    /**
     * @param $haystack
     * @param $needle
     *
     * @return bool
     */
    public function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * @param $haystack
     * @param $needle
     * @return bool
     */
    public function endsWith($haystack, $needle)
    {
        $length = strlen($needle);
        if($length === 0)
        {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }
}