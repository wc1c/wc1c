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
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class HttpClient
 *
 * @package Wsklad\MoySklad
 */
class HttpClient implements ClientInterface
{
	/**
	 * @var array<string, mixed>
	 */
	protected $options;

	/**
	 * HttpClient Constructor.
	 *
	 * @param array<string, mixed> $options
	 */
	public function __construct($options = [])
	{
		$this->options = $options;
	}

	/**
	 * @param RequestInterface $request
	 *
	 * @return ResponseInterface
	 */
	public function sendRequest($request)
	{
		$uri = $request->getUri();
		$args = $this->prepareArgs($request);
		$httpVer = $request->getProtocolVersion();

		$responseData = wp_remote_request($uri, $args);

		$code = wp_remote_retrieve_response_code($responseData);
		$code = is_numeric($code) ? (int) $code : 400;

		$reason = wp_remote_retrieve_response_message($responseData);

		$headers = wp_remote_retrieve_headers($responseData);
		$headers = is_array($headers) ? $headers : iterator_to_array($headers);

		$body = wp_remote_retrieve_body($responseData);

		return new Response($code, $headers, $body, $httpVer, $reason);
	}

	/**
	 * Prepares the args array for a specific request.
	 * The result can be used with WordPress' remote functions.
	 *
	 * @param RequestInterface $request The request.
	 *
	 * @return array<string, mixed> The prepared args array.
	 */
	protected function prepareArgs(RequestInterface $request)
	{
		return array_merge($this->options,
		[
			'method' => $request->getMethod(),
			'httpversion' => $request->getProtocolVersion(),
			'headers' => $this->prepareHeaders($request),
			'body' => (string) $request->getBody(),
		]);
	}

	/**
	 * Transforms a request's headers into the format expected by WordPress' remote functions.
	 *
	 * @param RequestInterface $request The request.
	 *
	 * @return array<string, string> The prepared headers array.
	 */
	protected function prepareHeaders(RequestInterface $request)
	{
		$headers = [];

		foreach($request->getHeaders() as $header => $values)
		{
			$headers[$header] = $request->getHeaderLine($header);
		}

		return $headers;
	}
}