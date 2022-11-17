<?php namespace Digiom\Psr7wp;

defined('ABSPATH') || exit;

use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriInterface;

/**
 * Class ServerRequest - Server-side HTTP request
 *
 * Extends the Request definition to add methods for accessing incoming data,
 * specifically server parameters, cookies, matched path parameters, query
 * string arguments, body parameters, and upload file information.
 *
 * "Attributes" are discovered via decomposing the request (and usually
 * specifically the URI path), and typically will be injected by the application.
 *
 * Requests are considered immutable; all methods that might change state are
 * implemented such that they retain the internal state of the current
 * message and return a new instance that contains the changed state.
 *
 * @package Digiom\Psr7wp
 */
class ServerRequest extends Request implements ServerRequestInterface
{
	/**
	 * @var array
	 */
	private $attributes = [];

	/**
	 * @var array
	 */
	private $cookieParams = [];

	/**
	 * @var array|object|null
	 */
	private $parsedBody;

	/**
	 * @var array
	 */
	private $queryParams = [];

	/**
	 * @var array
	 */
	private $serverParams;

	/**
	 * @var array
	 */
	private $uploadedFiles = [];

	/**
	 * ServerRequest constructor.
	 *
	 * @param string $method HTTP method
	 * @param string|UriInterface $uri URI
	 * @param array<string, string|string[]> $headers Request headers
	 * @param string|resource|StreamInterface|null $body Request body
	 * @param string $version Protocol version
	 * @param array $serverParams Typically the $_SERVER superglobal
	 */
	public function __construct($method, $uri, array $headers = [], $body = null, $version = '1.1', array $serverParams = [])
	{
		$this->serverParams = $serverParams;

		parent::__construct($method, $uri, $headers, $body, $version);
	}

	/**
	 * Return an UploadedFile instance array.
	 *
	 * @param array $files A array which respect $_FILES structure
	 *
	 * @throws InvalidArgumentException for unrecognized values
	 */
	public static function normalizeFiles(array $files)
	{
		$normalized = [];

		foreach ($files as $key => $value)
		{
			if($value instanceof UploadedFileInterface)
			{
				$normalized[$key] = $value;
			}
			elseif(is_array($value))
			{
				if(isset($value['tmp_name']))
				{
					$normalized[$key] = self::createUploadedFileFromSpec($value);
				}
				else
				{
					$normalized[$key] = self::normalizeFiles($value);
				}
			}
			else
			{
				throw new InvalidArgumentException('Invalid value in files specification');
			}
		}

		return $normalized;
	}

	/**
	 * Create and return an UploadedFile instance from a $_FILES specification.
	 *
	 * If the specification represents an array of values, this method will
	 * delegate to normalizeNestedFileSpec() and return that return value.
	 *
	 * @param array $value $_FILES struct
	 *
	 * @return UploadedFile|UploadedFileInterface[]
	 */
	private static function createUploadedFileFromSpec(array $value)
	{
		if(is_array($value['tmp_name']))
		{
			return self::normalizeNestedFileSpec($value);
		}

		return new UploadedFile($value['tmp_name'], (int) $value['size'], (int) $value['error'], $value['name'], $value['type']);
	}

	/**
	 * Normalize an array of file specifications.
	 *
	 * Loops through all nested files and returns a normalized array of
	 * UploadedFileInterface instances.
	 *
	 * @return UploadedFileInterface[]
	 */
	private static function normalizeNestedFileSpec(array $files = [])
	{
		$normalizedFiles = [];

		foreach (array_keys($files['tmp_name']) as $key)
		{
			$spec = [
				'tmp_name' => $files['tmp_name'][$key],
				'size'     => $files['size'][$key],
				'error'    => $files['error'][$key],
				'name'     => $files['name'][$key],
				'type'     => $files['type'][$key],
			];

			$normalizedFiles[$key] = self::createUploadedFileFromSpec($spec);
		}

		return $normalizedFiles;
	}

	/**
	 * Return a ServerRequest populated with superglobals:
	 * $_GET
	 * $_POST
	 * $_COOKIE
	 * $_FILES
	 * $_SERVER
	 */
	public static function fromGlobals()
	{
		$method = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
		$headers = getallheaders();
		$uri = self::getUriFromGlobals();

		$body = new CachingStream(new LazyOpenStream('php://input', 'r+'));

		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? str_replace('HTTP/', '', $_SERVER['SERVER_PROTOCOL']) : '1.1';

		$serverRequest = new ServerRequest($method, $uri, $headers, $body, $protocol, $_SERVER);

		return $serverRequest
			->withCookieParams($_COOKIE)
			->withQueryParams($_GET)
			->withParsedBody($_POST)
			->withUploadedFiles(self::normalizeFiles($_FILES));
	}

	/**
	 * @param $authority
	 *
	 * @return array|null[]
	 */
	private static function extractHostAndPortFromAuthority($authority)
	{
		$uri = 'http://' . $authority;
		$parts = parse_url($uri);
		if(false === $parts)
		{
			return [null, null];
		}

		$host = isset($parts['host']) ? $parts['host'] : null;
		$port = isset($parts['port']) ? $parts['port'] : null;

		return [$host, $port];
	}

	/**
	 * Get a Uri populated with values from $_SERVER.
	 */
	public static function getUriFromGlobals()
	{
		$uri = new Uri('');

		$uri = $uri->withScheme(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http');

		$hasPort = false;
		if(isset($_SERVER['HTTP_HOST']))
		{
			$host_port_array = self::extractHostAndPortFromAuthority($_SERVER['HTTP_HOST']);

			$host = isset($host_port_array['0']) ? $host_port_array['0'] : null;
			$port = isset($host_port_array['1']) ? $host_port_array['1'] : null;

			if($host !== null)
			{
				$uri = $uri->withHost($host);
			}

			if ($port !== null)
			{
				$hasPort = true;
				$uri = $uri->withPort($port);
			}
		}
		elseif (isset($_SERVER['SERVER_NAME']))
		{
			$uri = $uri->withHost($_SERVER['SERVER_NAME']);
		}
		elseif(isset($_SERVER['SERVER_ADDR']))
		{
			$uri = $uri->withHost($_SERVER['SERVER_ADDR']);
		}

		if (!$hasPort && isset($_SERVER['SERVER_PORT'])) {
			$uri = $uri->withPort($_SERVER['SERVER_PORT']);
		}

		$hasQuery = false;
		if(isset($_SERVER['REQUEST_URI']))
		{
			$requestUriParts = explode('?', $_SERVER['REQUEST_URI'], 2);
			$uri = $uri->withPath($requestUriParts[0]);
			if (isset($requestUriParts[1])) {
				$hasQuery = true;
				$uri = $uri->withQuery($requestUriParts[1]);
			}
		}

		if (!$hasQuery && isset($_SERVER['QUERY_STRING']))
		{
			$uri = $uri->withQuery($_SERVER['QUERY_STRING']);
		}

		return $uri;
	}

	/**
	 * @return array
	 */
	public function getServerParams()
	{
		return $this->serverParams;
	}

	/**
	 * @return array
	 */
	public function getUploadedFiles()
	{
		return $this->uploadedFiles;
	}

	/**
	 * @param array $uploadedFiles
	 *
	 * @return ServerRequest
	 */
	public function withUploadedFiles(array $uploadedFiles)
	{
		$new = clone $this;
		$new->uploadedFiles = $uploadedFiles;

		return $new;
	}

	/**
	 * @return array
	 */
	public function getCookieParams()
	{
		return $this->cookieParams;
	}

	/**
	 * @param array $cookies
	 *
	 * @return ServerRequest
	 */
	public function withCookieParams(array $cookies)
	{
		$new = clone $this;
		$new->cookieParams = $cookies;

		return $new;
	}

	/**
	 * @return array
	 */
	public function getQueryParams()
	{
		return $this->queryParams;
	}

	/**
	 * @param array $query
	 *
	 * @return ServerRequest
	 */
	public function withQueryParams(array $query)
	{
		$new = clone $this;
		$new->queryParams = $query;

		return $new;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return array|object|null
	 */
	public function getParsedBody()
	{
		return $this->parsedBody;
	}

	/**
	 * @param array|object|null $data
	 *
	 * @return ServerRequest
	 */
	public function withParsedBody($data)
	{
		$new = clone $this;
		$new->parsedBody = $data;

		return $new;
	}

	/**
	 * @return array
	 */
	public function getAttributes()
	{
		return $this->attributes;
	}

	/**
	 * {@inheritdoc}
	 *
	 * @return mixed
	 */
	public function getAttribute($attribute, $default = null)
	{
		if(false === array_key_exists($attribute, $this->attributes))
		{
			return $default;
		}

		return $this->attributes[$attribute];
	}

	/**
	 * @param string $attribute
	 * @param mixed $value
	 *
	 * @return ServerRequest
	 */
	public function withAttribute($attribute, $value)
	{
		$new = clone $this;
		$new->attributes[$attribute] = $value;

		return $new;
	}

	/**
	 * @param string $attribute
	 *
	 * @return $this|ServerRequest
	 */
	public function withoutAttribute($attribute)
	{
		if (false === array_key_exists($attribute, $this->attributes))
		{
			return $this;
		}

		$new = clone $this;
		unset($new->attributes[$attribute]);

		return $new;
	}
}
