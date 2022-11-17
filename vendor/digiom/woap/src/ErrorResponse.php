<?php namespace Digiom\Woap;

defined('ABSPATH') || exit;

use ErrorException;

/**
 * ErrorResponse
 *
 * @package Digiom\Woap
 */
class ErrorResponse extends ErrorException
{
	/**
	 * @var string
	 */
	private $info;

	/**
	 * @var array
	 */
    private $errors;

	/**
	 * @return string
	 */
	public function getInfo()
	{
		return $this->info;
	}

	/**
	 * @param string $info
	 */
	public function setInfo($info)
	{
		$this->info = $info;
	}

	/**
	 * @return array
	 */
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * @param array $errors
	 */
	public function setErrors($errors)
	{
		$this->errors = $errors;
	}
}