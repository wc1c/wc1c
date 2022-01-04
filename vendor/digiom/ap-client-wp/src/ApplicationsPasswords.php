<?php namespace Digiom\ApClientWP;

defined('ABSPATH') || exit;

use Exception;
use RuntimeException;
use Digiom\Psr7wp\HttpClient;
use Digiom\ApClientWP\Utils\StringsTrait;

/**
 * Class ApplicationsPasswords
 *
 * @package Digiom\ApClientWP
 */
class ApplicationsPasswords
{
	use StringsTrait;

	/**
	 * @var string
	 */
	private $host;

	/**
	 * @var string
	 */
	private $app_name;

	/**
	 * @var string
	 */
	private $login;

	/**
	 * @var string
	 */
	private $password;

	/**
	 * @var string
	 */
	private $token = '';

	/**
	 * @var HttpClient
	 */
	private $httpClient;

	/**
	 * ApiClient constructor. Создаёт экземпляр коннектора API
	 *
	 * @param string $host хост, на котором располагается API
	 * @param bool $forceHttps форсировать запрос через HTTPS
	 * @param array $credentials логин и пароль пользователя или токен пользователя
	 * @param HttpClient|null $http_client HTTP-клиент
	 *
	 * @throws Exception
	 */
	public function __construct($host, $forceHttps, $credentials, $http_client = null)
	{
		if(empty($host))
		{
			throw new RuntimeException('Hosts address cannot be empty or null!');
		}

		$host = trim($host);

		if($this->isInvalidCredentials($credentials))
		{
			throw new RuntimeException('Credential login, password or token must be set!');
		}

		while($this->endsWith($host, '/'))
		{
			$host = substr($host, 0, -1);
		}

		if($forceHttps)
		{
			if($this->startsWith($host, 'http://'))
			{
				$host = str_replace('http://', 'https://', $host);
			}
			elseif(!$this->startsWith($host, 'https://'))
			{
				$host = 'https://' . $host;
			}
		}
		elseif(!$this->startsWith($host, 'https://') && !$this->startsWith($host, 'http://'))
		{
			$host = 'http://' . $host;
		}

		$this->host = $host;

		if(is_null($http_client))
		{
			$http_client = new HttpClient();
		}

		$this->setHttpClient($http_client);

		$this->setCredentials($credentials);
	}

	/**
	 * Устанавливает данные доступа, которые используются для авторизации запросов к API
	 *
	 * @param array $credentials Массив данных для доступа
	 * [
	 *  login - логин в формате <code>[имя_пользователя]</code>
	 *  password - пароль
	 *  token - Токен авторизации
	 * ]
	 *
	 * @throws Exception
	 */
	public function setCredentials($credentials)
	{
		if(isset($credentials['login']) && isset($credentials['token']))
		{
			$this->setToken($credentials['token']);
		}
		elseif(isset($credentials['login']) && isset($credentials['password']))
		{
			$this->login = $credentials['login'];
			$this->password = $credentials['password'];
		}
		else
		{
			throw new RuntimeException('Credential login, password or token must be set!');
		}
	}

	/**
	 * Устанавливает Bearer токен авторизации запросов к API
	 *
	 * @param string $token Bearer токен авторизации
	 */
	public function setToken($token)
	{
		$this->token = $token;
	}

	/**
	 * Устанавливает пользовательский HTTP-клиент, с помощью которого будут выполняться запросы.
	 *
	 * @param HttpClient $client
	 */
	public function setHttpClient($client)
	{
		$this->httpClient = $client;
	}

	/**
	 * @return HttpClient
	 */
	public function getHttpClient()
	{
		return $this->httpClient;
	}

	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->host;
	}

	/**
	 * @return string
	 */
	public function getLogin()
	{
		return $this->login;
	}

	/**
	 * @return string
	 */
	public function getPassword()
	{
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getToken()
	{
		return $this->token;
	}

	/**
	 * @param array $credentials
	 *
	 * @return bool
	 */
	private function isInvalidCredentials($credentials)
	{
		return (!isset($credentials['login']) && !isset($credentials['password'])) && !isset($credentials['token']);
	}

	/**
	 * Удаление активного пароля приложений
	 *
	 * @return boolean
	 */
	public function deleteToken()
	{
		$path = '/deactivate';
	}

	/**
	 * Верификация текущих данных для подключения
	 *
	 * @return void
	 */
	public function verify()
	{

	}

	/**
	 * Создание пароля приложений на стороне сервиса
	 *
	 * @return boolean|string
	 */
	public function createToken()
	{

		return false;
	}

	/**
	 * Создание ссылки для перехода пользователя на авторизацию приложения
	 *
	 * @return string
	 */
	public function buildUrl($return)
	{
		return $this->getHost() . '/account/apps?action=authorize&return_url=' . $return .'&app_name=' . $this->app_name;
	}

	/**
	 * @return string
	 */
	public function getAppName()
	{
		return $this->app_name;
	}

	/**
	 * @param string $app_name
	 */
	public function setAppName($app_name)
	{
		$this->app_name = $app_name;
	}
}
