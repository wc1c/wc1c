<?php
/**
 * HTTP Request class
 *
 * @package Tecodes/Local
 */
class Tecodes_Local_Http_Request
{
    /**
     * Request url
     *
     * @var string
     */
    private $url;

    /**
     * Request method
     *
     * @var string
     */
    private $method;

    /**
     * Request parameters
     *
     * @var array
     */
    private $parameters;

    /**
     * Request headers
     *
     * @var array
     */
    private $headers;

    /**
     * Request body
     *
     * @var string
     */
    private $body;

	/**
	 * Initialize request
	 *
	 * @param string $url Request url
	 * @param string $method Request method
	 * @param array $parameters Request parameters
	 * @param array $headers Request headers
	 * @param string $body Request body
	 */
	public function __construct($url = '', $method = 'POST', $parameters = [], $headers = [], $body = '')
	{
		$this->url = $url;
		$this->method = $method;
		$this->parameters = $parameters;
		$this->headers = $headers;
		$this->body = $body;
	}

    /**
     * Set url
     *
     * @param string $url Request url
     */
    public function set_url($url)
    {
        $this->url = $url;
    }

    /**
     * Set method
     *
     * @param string $method Request method
     */
    public function set_method($method)
    {
        $this->method = $method;
    }

    /**
     * Set parameters.
     *
     * @param array $parameters Request parameters
     */
    public function set_parameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * Set headers
     *
     * @param array $headers Request headers
     */
    public function set_headers($headers)
    {
        $this->headers = $headers;
    }

    /**
     * Set body
     *
     * @param string $body Request body
     */
    public function set_body($body)
    {
        $this->body = $body;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function get_url()
    {
        return $this->url;
    }

    /**
     * Get method
     *
     * @return string
     */
    public function get_method()
    {
        return $this->method;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function get_parameters()
    {
        return $this->parameters;
    }

    /**
     * Get headers
     *
     * @return array
     */
    public function get_headers()
    {
        return $this->headers;
    }

    /**
     * Get raw headers
     *
     * @return array
     */
    public function get_raw_headers()
    {
        $headers = [];

        foreach ($this->headers as $key => $value)
        {
            $headers[] = $key . ': ' . $value;
        }

        return $headers;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function get_body()
    {
        return $this->body;
    }
}
