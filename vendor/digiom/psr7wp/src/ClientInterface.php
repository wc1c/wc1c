<?php namespace Digiom\Psr7wp;

defined('ABSPATH') || exit;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ClientInterface
 *
 * @package Wsklad\MoySklad\Http
 */
interface ClientInterface
{
    /**
     * Sends a PSR-7 request and returns a PSR-7 response.
     *
     * @param RequestInterface $request
     *
     * @return ResponseInterface
     * @throws ClientExceptionInterface If an error happens while processing the request.
     */
    public function sendRequest($request);
}
