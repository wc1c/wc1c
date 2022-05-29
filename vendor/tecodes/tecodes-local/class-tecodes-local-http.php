<?php
/**
 * HTTP class
 *
 * @package Tecodes/Local
 */
class Tecodes_Local_Local_Http implements Interface_Tecodes_Local_Http
{
    /**
     * cURL handle
     *
     * @var resource
     */
    protected $ch;

    /**
     * Client options
     *
     * @var Tecodes_Local_Http_Options
     */
    protected $options;

    /**
     * Request
     *
     * @var Tecodes_Local_Http_Request
     */
    private $request;

    /**
     * Response
     *
     * @var Tecodes_Local_Http_Response
     */
    private $response;

    /**
     * Response headers
     *
     * @var string
     */
    private $response_headers;

	/**
	 * Store API URL
	 *
	 * @var string
	 */
	protected $url;

	/**
	 * Consumer key.
	 *
	 * @var string
	 */
	protected $consumerKey = false;

	/**
	 * Consumer secret
	 *
	 * @var string
	 */
	protected $consumerSecret = false;

    /**
     * Initialize HTTP client
     *
     * @param array $options Client options
     *
     * @throws Exception
     */
    public function __construct($options)
    {
        if(!function_exists('curl_version'))
        {
            throw new Exception('cURL is NOT installed on this server');
        }

        $this->options = new Tecodes_Local_Http_Options($options);
    }

    /**
     * Check if is under SSL
     *
     * @return bool
     */
    protected function is_ssl()
    {
        return 'https://' === substr($this->url, 0, 8);
    }

    /**
     * Build API URL
     *
     * @param string $url Store URL
     *
     * @return string
     */
    protected function build_api_url($url)
    {
        return rtrim($url, '/') . $this->options->api_prefix() . $this->options->get_version() . '/';
    }

    /**
     * Build URL.
     *
     * @param string $url        URL.
     * @param array  $parameters Query string parameters.
     *
     * @return string
     */
    protected function build_url_query($url, $parameters = [])
    {
        if (!empty($parameters)) {
            $url .= '?' . \http_build_query($parameters);
        }

        return $url;
    }

    /**
     * Authenticate
     *
     * @param string $url Request URL
     * @param string $method Request method
     * @param array $parameters Request parameters
     *
     * @return array
     *
     * @throws Exception
     */
    protected function authenticate($url, $method, $parameters = [])
    {
        // Setup authentication
        if($this->is_ssl())
        {
            $basicAuth = new Tecodes_Local_Http_Basic_Auth
            (
                $this->ch,
                $this->consumerKey,
                $this->consumerSecret,
                $this->options->is_query_string_auth(),
                $parameters
            );

            $parameters = $basicAuth->get_parameters();
        }
        else
        {
            $oAuth = new Tecodes_Local_Http_OAuth
            (
                $url,
                $this->consumerKey,
                $this->consumerSecret,
                $this->options->get_version(),
                $method,
                $parameters,
                $this->options->oauth_timestamp()
            );

            $parameters = $oAuth->get_parameters();
        }

        return $parameters;
    }

    /**
     * Setup method
     *
     * @param string $method Request method
     */
    protected function setup_method($method)
    {
	    if('POST' == $method)
	    {
		    curl_setopt($this->ch, CURLOPT_POST, true);
	    }
	    elseif(in_array($method, ['PUT', 'DELETE', 'OPTIONS']))
	    {
		    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, $method);
	    }
    }

    /**
     * Get request headers
     *
     * @param bool $send_data If request send data or not
     *
     * @return array
     */
    protected function get_request_headers($send_data = false)
    {
        $headers =
	    [
            'Accept' => 'application/json',
            'User-Agent' => $this->options->user_agent(),
        ];

        if($send_data)
        {
            $headers['Content-Type'] = 'application/json;charset=utf-8';
        }

        return $headers;
    }

    /**
     * Create request
     *
     * @param string $endpoint Request endpoint
     * @param string $method Request method
     * @param array $data Request data
     * @param array $parameters Request parameters
     *
     * @return Tecodes_Local_Http_Request
     *
     * @throws Exception
     */
    protected function create_request($endpoint, $method, $data = [], $parameters = [])
    {
        $body = '';
	    $url = $this->build_api_url($this->url) . $endpoint;
        $has_data = !empty($data);

        // Setup authentication
        //$parameters = $this->authenticate($url, $method, $parameters);

        // Setup method
        $this->setup_method($method);

        // Include post fields
	    if($has_data)
	    {
		    $body = json_encode($data);
		    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $body);
	    }

        $this->request = new Tecodes_Local_Http_Request
        (
            $this->build_url_query($url, $parameters),
            $method,
            $parameters,
            $this->get_request_headers($has_data),
            $body
        );

        return $this->get_request();
    }

	/**
	 * @return string
	 */
	public function get_url()
	{
		return $this->url;
	}

	/**
	 * @param string $url
	 */
	public function set_url($url)
	{
		$this->url = $url;
	}

    /**
     * Get response headers
     *
     * @return array
     */
    protected function get_response_headers()
    {
        $headers = [];
        $lines = \explode("\n", $this->response_headers);
        $lines = \array_filter($lines, 'trim');

        foreach ($lines as $index => $line)
        {
            // Remove HTTP/xxx params
            if (strpos($line, ': ') === false)
            {
                continue;
            }

            list($key, $value) = \explode(': ', $line);

            $headers[$key] = isset($headers[$key]) ? $headers[$key] . ', ' . trim($value) : trim($value);
        }

        return $headers;
    }

    /**
     * Create response
     *
     * @return Tecodes_Local_Http_Response
     */
    protected function create_response()
    {
        // Set response headers
        $this->response_headers = '';

        curl_setopt($this->ch, CURLOPT_HEADERFUNCTION, function ($_, $headers)
        {
            $this->response_headers .= $headers;
            return strlen($headers);
        });

        // Get response data
        $body = curl_exec($this->ch);
        $code = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
        $headers = $this->get_response_headers();

        // Register response
        $this->response = new Tecodes_Local_Http_Response($code, $headers, $body);

        return $this->get_response();
    }

    /**
     * Set default cURL settings
     */
    protected function set_default_curl_settings()
    {
        $verify_ssl = $this->options->verify_ssl();
        $timeout = $this->options->get_timeout();
        $follow_redirects = $this->options->get_follow_redirects();

        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, $verify_ssl);

        if(!$verify_ssl)
        {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, $verify_ssl);
        }

        if($follow_redirects)
        {
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
        }

        curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $this->request->get_raw_headers());
        curl_setopt($this->ch, CURLOPT_URL, $this->request->get_url());
	    curl_setopt($this->ch, CURLOPT_REFERER, 'http://google.ru');
    }

    /**
     * Look for errors in the request
     *
     * @param array $parsed_response Parsed body response
     *
     * @throws Tecodes_Local_Http_Exception
     */
    protected function look_for_errors($parsed_response)
    {
        // Any non-200/201/202 response code indicates an error
        if (!in_array($this->response->get_code(), ['200', '201', '202']))
        {
            $errors = isset($parsed_response->errors) ? $parsed_response->errors : $parsed_response;
            $error_message = '';
            $error_code = '';

            if(is_array($errors))
            {
                $error_message = $errors[0]->message;
                $error_code = $errors[0]->code;
            }
            elseif(isset($errors->message, $errors->code))
            {
                $error_message = $errors->message;
                $error_code = $errors->code;
            }

            throw new Tecodes_Local_Http_Exception
            (
                sprintf('Error: %s [%s]', $error_message, $error_code),
                $this->response->get_code(),
                $this->request,
                $this->response
            );
        }
    }

    /**
     * Process response
     *
     * @return array
     *
     * @throws Tecodes_Local_Http_Exception
     */
    protected function process_response()
    {
        $body = $this->response->get_body();

        // Look for UTF-8 BOM and remove.
        if (0 === strpos(bin2hex(substr($body, 0, 4)), 'efbbbf'))
        {
            $body = substr($body, 3);
        }

        $parsedResponse = json_decode($body);

        // Test if return a valid JSON
        if(JSON_ERROR_NONE !== json_last_error())
        {
            $message = function_exists('json_last_error_msg') ? json_last_error_msg() : 'Invalid JSON returned';

            throw new Tecodes_Local_Http_Exception
            (
                sprintf('JSON ERROR: %s', $message),
                $this->response->get_code(),
                $this->request,
                $this->response
            );
        }

        $this->look_for_errors($parsedResponse);

        return $parsedResponse;
    }

    /**
     * Make requests
     *
     * @param string $endpoint Request endpoint
     * @param string $method Request method
     * @param array $data Request data
     * @param array $parameters Request parameters
     *
     * @return array
     *
     * @throws Tecodes_Local_Http_Exception
     */
    public function request($endpoint, $method, $data = [], $parameters = [])
    {
        // Initialize cURL
        $this->ch = curl_init();

        // Set request args
        $request = $this->create_request($endpoint, $method, $data, $parameters);
	  
        // Default cURL settings
        $this->set_default_curl_settings();

        // Get response
        $response = $this->create_response();

        // Check for cURL errors
        if(curl_errno($this->ch))
        {
            throw new Tecodes_Local_Http_Exception('cURL Error: ' . curl_error($this->ch), 0, $request, $response);
        }

        curl_close($this->ch);

        return $this->process_response();
    }

    /**
     * Get request data
     *
     * @return Tecodes_Local_Http_Request
     */
    public function get_request()
    {
        return $this->request;
    }

    /**
     * Get response data
     *
     * @return Tecodes_Local_Http_Response
     */
    public function get_response()
    {
        return $this->response;
    }
}
