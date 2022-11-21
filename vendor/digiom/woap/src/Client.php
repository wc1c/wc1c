<?php namespace Digiom\Woap;

defined('ABSPATH') || exit;

use Exception;
use RuntimeException;
use Psr\Http\Message\RequestInterface;
use Digiom\Psr7wp\RequestGet;
use Digiom\Psr7wp\HttpClient;
use Digiom\Woap\Utils\StringsTrait;

/**
 * Client
 *
 * @package Digiom\Woap
 */
class Client
{
	use StringsTrait;

	/**
	 * @var string
	 */
	protected $host;

	/**
	 * @var string
	 */
	protected $host_form_path = '/account/apps';

	/**
	 * @var string
	 */
	protected $app_name;

	/**
	 * @var string
	 */
	protected $login;

	/**
	 * @var string
	 */
	protected $password;

	/**
	 * @var string
	 */
	protected $token = '';

	/**
	 * @var HttpClient
	 */
	protected $httpClient;

	/**
	 * @var array
	 */
	protected $headers =
	[
		'Content-Type' => 'application/json',
		'Accept' => 'application/json;charset=utf-8',
	];

	/**
	 * ApiClient constructor. Создаёт экземпляр коннектора API
	 *
	 * @param string $host Хост, на котором располагается API
	 * @param bool $forceHttps Запросы через HTTPS
	 * @param array $credentials Логин и пароль пользователя или логин и токен пользователя
	 * @param HttpClient|null $http_client HTTP-клиент
	 *
	 * @throws Exception
	 */
	public function __construct($host = '', $forceHttps = true, $credentials = [], $http_client = null)
	{
		if($host !== '')
		{
			$host = trim($host);

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

			if(empty($host))
			{
				throw new RuntimeException('Hosts address cannot be empty or null!');
			}

			$this->host = $host;
		}

		if(is_null($http_client))
		{
			$http_client = new HttpClient();
		}

		$this->setHttpClient($http_client);

		if(!empty($credentials))
		{
			if($this->isInvalidCredentials($credentials))
			{
				throw new RuntimeException('Credential login, password or token must be set!');
			}

			$this->setCredentials($credentials);
		}
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
	 * @throws RuntimeException
	 */
	public function setCredentials($credentials)
	{
		if(isset($credentials['login'], $credentials['token']))
		{
			$this->login = $credentials['login'];
			$this->setToken($credentials['token']);
		}
		elseif(isset($credentials['login'], $credentials['password']))
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
		return (!isset($credentials['login']) && (!isset($credentials['password'], $credentials['token'])));
	}

	/**
	 * Верификация данных для подключения
	 *
	 * @param string $login User login
	 * @param string $password
	 *
	 * @return boolean|Exception
	 */
	public function verify($login = '', $password = '')
	{
		$credentials = [];

		if(!empty($login))
		{
			$credentials['login'] = $login;
		}

		if(!empty($password))
		{
			$credentials['token'] = $password;
		}

		if(!empty($credentials))
		{
			$this->setCredentials($credentials);
		}

		$uri = $this->getHost() . '/wp-json/wp/v2/users/me';

		$this->auth();

		$request = new RequestGet($uri, $this->headers);

		try
		{
			$response = $this->executeRequest($request);

			$response_array = json_decode($response, true);

			if(isset($response_array['id']))
			{
				return true;
			}
		}
		catch(Exception $e)
		{
			return $e;
		}

		return false;
	}

	/**
	 * Добавление параметра в заголовки запроса
	 *
	 * @param string $key
	 * @param string $value
	 *
	 * @return Client
	 */
	public function header($key, $value)
	{
		if('' !== $key)
		{
			$this->headers[$key] = $value;
		}

		return $this;
	}

	/**
	 * Добавляет авторизационный заголовок с данными доступа API
	 *
	 * @param Client $api
	 *
	 * @return Client
	 */
	private function auth($api = null)
	{
		if(is_null($api))
		{
			$api = $this;
		}

		if(empty($api->getToken()) && empty($api->getPassword()))
		{
			return $api;
		}

		$this->header('Referer', $this->host . '/wp-admin/');

		if($api->getToken())
		{
			return $this->header('Authorization', 'Basic ' . base64_encode($api->getLogin() . ':' . $api->getToken()));
		}

		return $this->header('Authorization', 'Basic ' . base64_encode($api->getLogin() . ':' . $api->getPassword()));
	}

	/**
	 * Создание ссылки для перехода пользователя на авторизацию приложения
	 *
	 * @return string
	 */
	public function buildUrl($return)
	{
		return $this->getHost() . $this->host_form_path . '?action=authorize&return_url=' . urlencode($return) .'&app_name=' . urlencode($this->getAppName());
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

	/**
	 * Выполнение запроса
	 *
	 * @param RequestInterface $request
	 *
	 * @return string Тело ответа
	 * @throws ApiException При возникновении ошибки API
	 */
	private function executeRequest($request)
	{
		try
		{
			$response = $this->httpClient->sendRequest($request);

			if($this->isBadResponse($response))
			{
				throw new ApiException($request->getMethod() . ' ' . $request->getUri(), $response->getStatusCode(), $response->getReasonPhrase());
			}

			return $response->getBody()->getContents();
		}
		catch(Exception $e)
		{
			throw new ApiException($request->getMethod() . ' ' . $request->getUri(), $e->getCode(), $e);
		}
	}

	/**
	 * Bad response
	 *
	 * @return bool
	 */
	public function isBadResponse($response)
	{
		$statusCode = (int) $response->getStatusCode();

		return $statusCode !== 200 && $statusCode !== 201 && $statusCode !== 204;
	}
}
