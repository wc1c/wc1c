<?php namespace Wc1c;

defined('ABSPATH') || exit;

use Exception;
use Digiom\ApClientWP\ApplicationsPasswords;
use Wc1c\Traits\SingletonTrait;

/**
 * Connection
 *
 * @package Wc1c
 */
final class Connection extends ApplicationsPasswords
{
	use SingletonTrait;

	/**
	 * Connection constructor.
	 *
	 * @throws Exception
	 */
	public function __construct($login = '', $password = '', $use_token = false)
	{
		$credentials['login'] = $login;

		if($use_token)
		{
			$credentials['token'] = $password;
		}
		else
		{
			$credentials['password'] = $password;
		}

		parent::__construct('https://wc1c.info', true, $credentials);
	}
}