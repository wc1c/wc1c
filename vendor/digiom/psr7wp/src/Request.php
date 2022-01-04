<?php
/**
 * Namespace
 */
namespace Digiom\Psr7wp;

/**
 * Only WordPress
 */
defined('ABSPATH') || exit;

/**
 * Dependencies
 */
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class Request
 *
 * @package Digiom\Psr7wp
 */
class Request implements RequestInterface
{
	use MessageTrait;

	/**
	 * @var string
	 */
	private $method;

	/**
	 * @var
	 */
	private $requestTarget;

	/**
	 * @var UriInterface|string|Uri
	 */
	private $uri;

	/**
	 * @param string $method  HTTP method
	 * @param string|UriInterface $uri URI
	 * @param array<string, string|string[]> $headers Request headers
	 * @param string|resource|StreamInterface|null $body Request body
	 * @param string $version Protocol version
	 */
	public function __construct($method, $uri, array $headers = [], $body = null, $version = '1.1')
	{
		$this->assertMethod($method);

		if(!($uri instanceof UriInterface))
		{
			$uri = new Uri($uri);
		}

		$this->method = strtoupper($method);
		$this->uri = $uri;
		$this->setHeaders($headers);
		$this->protocol = $version;

		if(!isset($this->headerNames['host']))
		{
			$this->updateHostFromUri();
		}

		if($body !== '' && $body !== null)
		{
			$this->stream = Utils::streamFor($body);
		}
	}

	/**
	 * @return string
	 */
	public function getRequestTarget()
	{
		if($this->requestTarget !== null)
		{
			return $this->requestTarget;
		}

		$target = $this->uri->getPath();

		if($target === '')
		{
			$target = '/';
		}

		if($this->uri->getQuery() !== '')
		{
			$target .= '?' . $this->uri->getQuery();
		}

		return $target;
	}

	/**
	 * @param mixed $requestTarget
	 *
	 * @return Request
	 */
	public function withRequestTarget($requestTarget)
	{
		if(preg_match('#\s#', $requestTarget))
		{
			throw new InvalidArgumentException('Invalid request target provided; cannot contain whitespace');
		}

		$new = clone $this;
		$new->requestTarget = $requestTarget;

		return $new;
	}

	/**
	 * @return string
	 */
	public function getMethod()
	{
		return $this->method;
	}

	/**
	 * @param string $method
	 *
	 * @return Request
	 */
	public function withMethod($method)
	{
		$this->assertMethod($method);

		$new = clone $this;
		$new->method = strtoupper($method);

		return $new;
	}

	/**
	 * @return UriInterface|string|Uri
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * @param UriInterface $uri
	 * @param false $preserveHost
	 *
	 * @return $this|Request
	 */
	public function withUri(UriInterface $uri, $preserveHost = false)
	{
		if($uri === $this->uri)
		{
			return $this;
		}

		$new = clone $this;
		$new->uri = $uri;

		if(!$preserveHost || !isset($this->headerNames['host']))
		{
			$new->updateHostFromUri();
		}

		return $new;
	}

	/**
	 * @return void
	 */
	private function updateHostFromUri()
	{
		$host = $this->uri->getHost();

		if($host === '')
		{
			return;
		}

		if(($port = $this->uri->getPort()) !== null)
		{
			$host .= ':' . $port;
		}

		if(isset($this->headerNames['host']))
		{
			$header = $this->headerNames['host'];
		}
		else
		{
			$header = 'Host';
			$this->headerNames['host'] = 'Host';
		}

		// Ensure Host is the first header.
		// See: http://tools.ietf.org/html/rfc7230#section-5.4
		$this->headers = [$header => [$host]] + $this->headers;
	}

	/**
	 * @param mixed $method
	 */
	private function assertMethod($method)
	{
		if(!is_string($method) || $method === '')
		{
			throw new InvalidArgumentException('Method must be a non-empty string.');
		}
	}
}