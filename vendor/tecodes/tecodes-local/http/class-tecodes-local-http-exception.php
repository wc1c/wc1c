<?php
/**
 * HTTP Exception class
 *
 * @package Tecodes/Local
 */
class Tecodes_Local_Http_Exception extends Exception
{
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
	 * Initialize exception.
	 *
	 * @param string $message Error message.
	 * @param int $code Error code.
	 * @param Tecodes_Local_Http_Request $request Request data.
	 * @param Tecodes_Local_Http_Response $response Response data.
	 */
	public function __construct($message, $code, Tecodes_Local_Http_Request $request, Tecodes_Local_Http_Response $response)
	{
		parent::__construct($message, $code);

		$this->request  = $request;
		$this->response = $response;
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
