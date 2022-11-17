<?php namespace Digiom\Psr7wp;

defined('ABSPATH') || exit;

/**
 * Class RequestGet
 *
 * @package Digiom\Psr7wp
 */
class RequestGet extends Request
{
	const METHOD = 'GET';

	/**
	 * RequestGet constructor.
	 *
	 * @param $uri
	 * @param array $headers
	 * @param null $body
	 * @param string $version
	 */
	public function __construct($uri, $headers = [], $body = null, $version = '1.1')
	{
		parent::__construct($this->getMethod(), $uri, $headers, $body, $version);
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return self::METHOD;
	}
}