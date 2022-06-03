<?php
/**
 * Main class
 *
 * @package Tecodes/Local
 */
class Tecodes_Local implements Interface_Tecodes_Local
{
	/**
	 * @var bool|array
	 */
	protected $errors = false;

	/**
	 * HTTP client
	 *
	 * @var null
	 */
	protected $http = null;

	/**
	 * Полный адрес сервера API
	 *
	 * @var string
	 */
	protected $api_server = '';

	/**
	 * @var null|Tecodes_Local_Instance
	 */
	protected $instance = null;

	/**
	 * Текущий код
	 *
	 * @var string
	 */
	protected $code = false;

	/**
	 * Секретный набор данных
	 * Практически не используется в GPL.
	 *
	 * @var bool|string
	 */
	protected $secret = false;

	/**
	 * Маркер проверки секретности
	 *
	 * @var bool
	 */
	protected $use_secret = false;

	/**
	 * Локальный код
	 *
	 * @var bool|string
	 */
	protected $local_code = false;

	/**
	 * Маркер режима хранения ключа
	 *
	 * @var null|Interface_Tecodes_Local_Storage_Code
	 */
	protected $local_code_storage = null;

	/**
	 * Периоды в днях, в течении которого локальный код считается действителен
	 * Можно указать несколько значений через запятую. Тогда запросы будут происходить по нарастающей
	 *
	 * @var string
	 */
	protected $local_code_delay_period = '1,2,3,4,5,6,7,8,9';

	/**
	 * Маркер использования на локальной системе с Windows без активации
	 *
	 * @var boolean
	 */
	protected $use_localhost = false;

	/**
	 * Маркер использования текущей версию после истечении срока действия кода активации
	 *
	 * NOTE: Если истина, то локальный ключ будет продолжать работать, даже после истечения срока действия кода активации.
	 * Локальный код будет работать, только на старых релизах скрипта, на новой версии валидация уже будет не действительна.
	 *
	 * @var boolean
	 */
	protected $use_after_expires = true;

	/**
	 * Локализация статусов и сообщений
	 *
	 * @var array
	 */
	public $status_messages = array
	(
		'status_1' => 'This activation code is active.',
		'status_2' => 'Error: This activation code has expired.',
		'status_3' => 'Activation code republished. Awaiting reactivation.',
		'status_4' => 'Error: This activation code has been suspended.',
		'code_not_found' => 'This activation code is not found.',
		'localhost' => 'This activation code is active (localhost).',
		'pending' => 'Error: This activation code is pending review.',
		'download_access_expired' => 'Error: This version of the software was released after your download access expired. Please downgrade software or contact support for more information.',
		'missing_activation_key' => 'Error: The activation code variable is empty.',
		'could_not_obtain_local_code' => 'Error: I could not obtain a new local code.',
		'maximum_delay_period_expired' => 'Error: The maximum local code delay period has expired.',
		'local_code_tampering' => 'Error: The local key has been tampered with or is invalid.',
		'local_code_invalid_for_location' => 'Error: The local code is invalid for this location.',
		'missing_license_file' => 'Error: Please create the following file (and directories if they dont exist already): ',
		'license_file_not_writable' => 'Error: Please make the following path writable: ',
		'invalid_local_key_storage' => 'Error: I could not determine the local key storage on clear.',
		'could_not_save_local_key' => 'Error: I could not save the local key.',
		'code_string_mismatch' => 'Error: The local code is invalid for this activation code.',
		'code_status_delete' => 'Error: This activation code has been deleted.',
		'code_status_draft' => 'Error: This activation code has draft.',
		'code_status_available' => 'Error: This activation code has available.',
		'code_status_blocked' => 'Error: This activation code has been blocked.',
	);

	/**
	 * Validation status
	 *
	 * @var bool
	 */
	protected $valid = false;

	/**
	 * Tecodes_Local constructor
	 *
	 * @param string $api_server
	 * @param array $options
	 */
	public function __construct($api_server = '', $options = null)
	{
		if(is_null($options))
		{
			$options =
			[
				'timeout' => 30,
				'verify_ssl' => false,
				'version' => 'tecodes/v1'
			];
		}

		try
		{
			$http = new Tecodes_Local_Local_Http($options);
		}
		catch(Exception $e)
		{
			return;
		}

		if($http)
		{
			$this->set_http($http);

			if(!empty($api_server))
			{
				$this->api_set_server($api_server);
			}
		}
	}

	/**
	 * @param string $endpoint
	 * @param array $data
	 *
	 * @return array
	 */
	public function api_post($endpoint, $data)
	{
		return $this->http->request($endpoint, 'POST', $data);
	}

	/**
	 * @param string $endpoint
	 * @param array $data
	 *
	 * @return array
	 */
	public function api_put($endpoint, $data)
	{
		return $this->http->request($endpoint, 'PUT', $data);
	}

	/**
	 * @param string $endpoint
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function api_get($endpoint, $parameters = [])
	{
		return $this->http->request($endpoint, 'GET', [], $parameters);
	}

	/**
	 * @param string $endpoint
	 * @param array $parameters
	 *
	 * @return array
	 */
	public function api_delete($endpoint, $parameters = [])
	{
		return $this->http->request($endpoint, 'DELETE', [], $parameters);
	}

	/**
	 * @param string $endpoint
	 *
	 * @return array
	 */
	public function api_options($endpoint)
	{
		return $this->http->request($endpoint, 'OPTIONS', [], []);
	}

	/**
	 * Установка клиента для запросов
	 *
	 * @param Interface_Tecodes_Local_Http $http
	 *
	 * @return bool
	 */
	public function set_http($http)
	{
		if($http instanceof Interface_Tecodes_Local_Http)
		{
			$this->http = $http;
			return true;
		}

		return false;
	}

	/**
	 * Установка сервера API
	 *
	 * @param $server
	 *
	 * @return bool
	 */
	public function api_set_server($server)
	{
		if($server !== '')
		{
			if(!is_null($this->http))
			{
				$this->http->set_url($server);
			}

			$this->api_server = $server;

			return true;
		}

		return false;
	}

	/**
	 * Получение данных статуса API
	 */
	public function api_get_status_data()
	{
		return $this->api_get('status');
	}

	/**
	 * Текущий статус API
	 *
	 * @return string
	 */
	public function api_get_status()
	{
		$status = 'inactive';

		try
		{
			$data = $this->api_get_status_data();
		}
		catch(Exception $e)
		{
			$status = $e->getMessage();
		}

		if(isset($data->code) && $data->code == 'active')
		{
			$status = 'active';
		}

		return $status;
	}

	/**
	 * @param string $code
	 *
	 * @return array|bool
	 */
	public function api_get_code_data_by_name($code = '')
	{
		if($code === '')
		{
			$code = $this->get_code();
		}
		
		try
		{
			$data = $this->api_get('codes/' . $code);
		}
		catch(Exception $e)
		{
			return false;
		}

		if(isset($data->success) && $data->success == true && isset($data->data))
		{
			return $data->data;
		}

		return false;
	}

	/**
	 * Получение локального кода с сервера
	 *
	 * @param string $signature
	 *
	 * @return bool|string
	 */
	public function api_get_local_code($signature = '')
	{
		$instance_data = array();
		$instance_endpoint = 'instances';

		if($this->get_code() && $signature === '')
		{
			$instance_endpoint .= '/' . $this->get_code();
		}

		if($signature !== '')
		{
			$instance_endpoint .= '/' . $signature;
		}

		$instance_data['version'] = 1;
		$instance_data['access'] = $this->instance->access_details();
		
		try
		{
			$data = $this->api_post($instance_endpoint, $instance_data);
		}
		catch(Exception $e)
		{
			return false;
		}

		if(isset($data->success) && $data->success == true && isset($data->data))
		{
			return $data->data;
		}

		return false;
	}

	/**
	 * Установка текущего кода
	 *
	 * @param $code
	 *
	 * @return bool|void
	 */
	public function set_code($code)
	{
		$this->code = $code;
	}

	/**
	 * Получение текущего кода
	 *
	 * @return bool|string
	 */
	public function get_code()
	{
		return $this->code;
	}

	/**
	 * Установка локального кода
	 *
	 * @param $code
	 *
	 * @return bool
	 */
	public function set_local_code($code)
	{
		$this->local_code = $code;
		return true;
	}

	/**
	 * Установка хранилища локального кода
	 *
	 * @param Interface_Tecodes_Local_Storage_Code $storage
	 *
	 * @return bool|void
	 */
	public function set_local_code_storage($storage)
	{
		if($storage instanceof Interface_Tecodes_Local_Storage_Code)
		{
			$this->local_code_storage = $storage;

			return true;
		}

		return false;
	}

	/**
	 * Получение текущего локального кода
	 *
	 * @return bool|string
	 */
	public function get_local_code()
	{
		return $this->local_code_storage->read();
	}

	/**
	 * Сохранение локального кода
	 *
	 * @param string $local_code
	 *
	 * @return bool
	 */
	public function update_local_code($local_code)
	{
		return $this->local_code_storage->update($local_code);
	}

	/**
	 * Удаление текущего локального кода
	 *
	 * @return bool
	 */
	public function delete_local_code()
	{
		return $this->local_code_storage->delete();
	}

	/**
	 * Декодируем локальный ключ
	 *
	 * @param string $local_code
	 *
	 * @return string
	 */
	private function decode_local_code($local_code)
	{
		return base64_decode(str_replace("\n", '', urldecode($local_code)));
	}

	/**
	 * Разбиваем локальный ключ на части
	 *
	 * @param string $local_code
	 * @param string $token {tecodes} or \n\n
	 *
	 * @return string
	 */
	private function split_local_code($local_code, $token = '{tecodes}')
	{
		return explode($token, $local_code);
	}

	/**
	 * Расчитываем максимальное время действия льготного периода
	 *
	 * @param integer $local_key_expires Время действия локального ключа в UNIX формате
	 * @param integer $delay Дополнительный срок действия в днях
	 *
	 * @return integer
	 */
	private function calc_max_delay($local_key_expires, $delay)
	{
		return ((integer)$local_key_expires + ((integer)$delay * 86400));
	}

	/**
	 * Обработка льготного периода для локального ключа
	 *
	 * @param string $local_code
	 *
	 * @return array|mixed
	 */
	private function process_delay_period($local_code)
	{
		$local_code_data = $this->get_local_code_data($local_code);

		if(!is_array($local_code_data))
		{
			return false;
		}

		$local_code_expires = (integer)$local_code_data['local_code_expires'];

		$write_new_key = false;

		$parts = explode("\n\n", $local_code);
		$local_code = $parts[0];

		$local_code_delay_periods = explode(',', $this->local_code_delay_period);

		foreach($local_code_delay_periods as $delay)
		{
			if(!$delay)
			{
				$local_code .= "\n";
			}

			if($this->calc_max_delay($local_code_expires, $delay) > time())
			{
				continue;
			}

			$local_code .= "\n{$delay}";

			$write_new_key = true;
		}

		if(time() > $this->calc_max_delay($local_code_expires, array_pop($local_code_delay_periods)))
		{
			return array('write' => false, 'local_code' => '', 'errors' => $this->status_messages['maximum_delay_period_expired']);
		}

		return array('write' => $write_new_key, 'local_code' => $local_code, 'errors' => false);
	}

	/**
	 * Проверка на принадлежность к льготному периоду
	 *
	 * @param string $local_code
	 * @param integer $local_code_expires
	 *
	 * @return integer
	 */
	private function in_delay_period($local_code, $local_code_expires)
	{
		$delay = $this->split_local_code($local_code, "\n");

		if(!isset($delay[1]))
		{
			return -1;
		}

		$array = explode("\n", $delay[1]);

		return (integer)($this->calc_max_delay($local_code_expires, array_pop($array)) - time());
	}

	/**
	 * Проверяем дейтвия кода по параметрам доступа
	 *
	 * @param string $key
	 * @param array $valid_accesses
	 *
	 * @return bool
	 */
	private function validate_access($key, $valid_accesses)
	{
		return in_array($key, (array)$valid_accesses, false);
	}

	/**
	 * Получаем определенный набор деталей доступа из экземпляра
	 *
	 * @param array $instances
	 * @param string $enforce
	 *
	 * @return array
	 */
	private function extract_access_set($instances, $enforce)
	{
		$return = array();

		foreach($instances as $key => $instance)
		{
			if ($key !== $enforce)
			{
				continue;
			}

			$return = $instance;
		}

		return $return;
	}

	/**
	 * Получаем массив возможных IP адресов
	 *
	 * @param string $key
	 *
	 * @return array
	 */
	private function wildcard_ip($key)
	{
		$octets = explode('.', $key);

		array_pop($octets);
		$ip_range[] = implode('.', $octets) . '.*';

		array_pop($octets);
		$ip_range[] = implode('.', $octets) . '.*';

		array_pop($octets);
		$ip_range[] = implode('.', $octets) . '.*';

		return $ip_range;
	}

	/**
	 * Получаем server hostname с учетом wildcard
	 *
	 * @param string $key
	 *
	 * @return string
	 */
	private function wildcard_server_hostname($key)
	{
		$hostname = explode('.', $key);
		unset($hostname[0]);

		$hostname = (!isset($hostname[1])) ? array($key) : $hostname;

		return '*.' . implode('.', $hostname);
	}

	/**
	 * @return bool|void
	 */
	public function is_valid()
	{
		return $this->valid;
	}

	/**
	 * @param bool $value
	 */
	protected function set_valid($value = false)
	{
		$this->valid = $value;
	}

	/**
	 * Получение данных локального кода
	 *
	 * @param $local_code
	 *
	 * @return bool|array
	 */
	public function get_local_code_data($local_code)
	{
		/**
		 * Преобразование в удобную форму
		 */
		$source_local_code = $this->decode_local_code($local_code);

		/**
		 * Разделяем локальный ключ на партии
		 */
		$parts_local_code = $this->split_local_code($source_local_code);

		/**
		 * Проверяем на наличие всех частей локального ключа, если нет, то мы не можем проверять дальше
		 */
		if(!isset($parts_local_code[1]))
		{
			return false;
		}

		/**
		 * Проверяем секретный ключ на подделку. Если не совпадают, то возвратим ошибку
		 */
		if($this->use_secret)
		{
			if(md5((string)$this->secret . (string)$parts_local_code[1]) !== $parts_local_code[2])
			{
				return false;
			}
			unset($this->secret);
		}

		return unserialize($parts_local_code[1]);
	}

	/**
	 * Проверка локального кода
	 *
	 * @param $local_code
	 *
	 * @return bool
	 */
	public function validate_local_code($local_code)
	{
		/**
		 * Получение данных локального кода в массиве
		 */
		$local_code_data = $this->get_local_code_data($local_code);
		if(!is_array($local_code_data))
		{
			$this->add_error('local_code_tampering', $this->status_messages['local_code_tampering']);
			return false;
		}

		/**
		 * Проверяем статус кода, если он не активен возвращаем ошибку
		 */
		if(empty($local_code_data['code_status']) || ($local_code_data['code_status'] !== 'active' && $local_code_data['code_status'] !== 'inactive'))
		{
			$status = $this->status_messages['code_not_found'];

			if(isset($this->status_messages['code_status_' . $local_code_data['code_status']]))
			{
				$status = $this->status_messages['code_status_' . $local_code_data['code_status']];
			}

			$this->local_code_storage->delete();

			$this->add_error('code_status' . $local_code_data['code_status'], $status);
			return false;
		}

		/**
		 * Проверяем статус экземпляра, если он не активен возвращаем ошибку
		 */
		if(empty($local_code_data['instance_status']) || $local_code_data['instance_status'] !== 'active')
		{
			$status = $this->status_messages['code_not_found'];

			if(isset($this->status_messages['code_status_' . $local_code_data['instance_status']]))
			{
				$status = $this->status_messages['code_status_' . $local_code_data['instance_status']];
			}

			$this->local_code_storage->delete();

			$this->add_error('instance_status_' . $local_code_data['instance_status'], $status);
			return false;
		}

		/**
		 * Проверяем срок окончания кода
		 * NOTE: если срок ключа активации истек и стоит запрет на использование после истечение срока, выдаем ошибку.
		 */
		if($this->use_after_expires === false && (string)$local_code_data['code_date_expires'] !== 'never' && (integer)$local_code_data['code_date_expires'] < time())
		{
			$this->add_error('status_2', $this->status_messages['status_2']);
			return false;
		}

		/**
		 * Проверяем срок истечения локального кода
		 *
		 * Если срок истек, получаем новый с сервера
		 * Если не удалось получить с сервера, проверяем льготный период
		 * - если льготный период есть, продолжаем работу
		 * - если нет льготного периода, удаляем локальный ключ
		 */
		if(empty($local_code_data['local_code_expires']) || ((string) $local_code_data['local_code_expires'] !== 'never' && (integer) $local_code_data['local_code_expires'] <= time()))
		{
			$new_local_code = false;

			try
			{
				$new_local_code = $this->api_get_local_code($local_code_data['instance_signature']);
			}
			catch(Exception $e){}

			if($new_local_code)
			{
				$this->local_code_storage->update($new_local_code);
				return $this->validate();
			}

			/**
			 * Срок локального кода истек, и не удалось получить новый с сервера
			 */
			if($this->in_delay_period($local_code, $local_code_data['local_code_expires']) < 0)
			{
				$data = $this->process_delay_period($local_code);

				if(!is_array($data))
				{
					$this->local_code_storage->delete();
					$this->add_error('local_code_tampering', $this->status_messages['local_code_tampering']);
					return false;
				}

				if($data['errors'] !== false)
				{
					$this->add_error('local_code_tampering', $data['errors']);
					$this->local_code_storage->delete();
					return false;
				}

				if($data['write'] !== false)
				{
					$this->local_code_storage->update($data['local_code']);
				}
			}
		}

		/**
		 * Проверяем права на запуск для текущего окружения:
		 *
		 * - Проверяем домен. Домен проверяется сразу на поддомены, на разные зоны.
		 * - Проверяем IP адрес сервера.
		 * - Проверяем имя сервера.
		 */
		$conflicts = array();
		$access_details = $this->instance->access_details();

		$instance = $local_code_data['instance'];
		$enforce = $local_code_data['enforce'];

		foreach((array)$enforce as $key)
		{
			$valid_accesses = $this->extract_access_set($instance, $key);

			if(!$this->validate_access($access_details[$key], $valid_accesses))
			{
				$conflicts[$key] = true;

				if(in_array($key, array('ip', 'server_ip'), false))
				{
					foreach($this->wildcard_ip($access_details[$key]) as $ip)
					{
						if ($this->validate_access($ip, $valid_accesses))
						{
							unset($conflicts[$key]);
							break;
						}
					}
				}
				elseif(in_array($key, array('domain'), false))
				{
					if(isset($code_data['domain_wildcard']))
					{
						if($code_data['domain_wildcard'] == 1 && preg_match("/" . $valid_accesses[0] . "\z/i", $access_details[$key]))
						{
							$access_details[$key] = '*.' . $valid_accesses[0];
						}
						if($code_data['domain_wildcard'] == 2)
						{
							$exp_domain = explode('.', $valid_accesses[0]);
							$exp_domain = $exp_domain[0];
							if(preg_match("/".$exp_domain."/i", $access_details[$key]))
							{
								$access_details[$key] = '*.' . $valid_accesses[0] . '.*';
							}
						}
						if($code_data['domain_wildcard'] == 3)
						{
							$exp_domain = explode('.', $valid_accesses[0]);
							$exp_domain = $exp_domain[0];

							if(preg_match("/\A" . $exp_domain . "/i", $access_details[$key]))
							{
								$access_details[$key] = $valid_accesses[0] . '.*';
							}
						}
					}
					if ($this->validate_access($access_details[$key], $valid_accesses))
					{
						unset($conflicts[$key]);
					}
				}
				elseif(in_array($key, array('server_hostname'), false))
				{
					if ($this->validate_access($this->wildcard_server_hostname($access_details[$key]), $valid_accesses))
					{
						unset($conflicts[$key]);
					}
				}
			}
		}

		/**
         * Если конфликты для локального ключа остались, выдаем ошибку.
         * Скрипт не имеет права выполняться в данном расположении по указанной лицензии.
         */
		if(count($conflicts) !== 0)
		{
			$this->add_error('local_code_invalid_for_location', $this->status_messages['local_code_invalid_for_location']);
			return false;
		}

		$this->set_valid(true);

		return true;
	}

	/**
	 * Mini debug
	 *
	 * @param $data
	 * @param string $title
	 */
	public function debug($data, $title = '')
	{
		echo $title . '<br/>';
		echo '<pre>';
		var_dump($data);
		echo '</pre>';
	}

	/**
	 * Валидация всех механизмов
	 *
	 * @return bool
	 */
	public function validate()
	{
		$this->errors = false;

		/**
		 * Текущий локальный код
		 */
		$local_code = $this->local_code_storage->read();

		/**
		 * Разрешено использование без фактической активации
		 */
		if($this->use_localhost && $this->instance->get_ip_local() && $this->instance->is_windows() && !$local_code)
		{
			$this->set_valid(true);
			return true;
		}

		/**
		 * Если локальный код не найден, и не указан код активации
		 */
		if(($local_code === false || $local_code === '') && $this->get_code() === false)
		{
			$this->add_error('code_and_local_error', $this->status_messages['local_code_tampering']);
			return false;
		}

		/**
		 * Получаем новый локальный код для экземпляра с сервера
		 */
		if($local_code === false || $local_code === '')
		{
			try
			{
				$code_data = $this->api_get_code_data_by_name($this->get_code());
			}
			catch(Exception $e)
			{
				$this->add_error('api_get_code_data_by_name', $e->getMessage());
				return false;
			}

			if(!$code_data)
			{
				$this->add_error('api_get_code_data_by_name', $this->status_messages['code_not_found']);
				return false;
			}

			if(!isset($code_data->status))
			{
				$this->add_error('api_get_code_data_by_name', $this->status_messages['code_not_found']);
				return false;
			}

			if($code_data->status != 'active' && $code_data->status != 'inactive')
			{
				$status = $this->status_messages['code_not_found'];

				if(isset($this->status_messages['code_status_' . $code_data->status]))
				{
					$status = $this->status_messages['code_status_' . $code_data->status];
				}

				$this->add_error('api_get_code_data_by_name', $status);
				return false;
			}

			try
			{
				$local_code = $this->api_get_local_code();
			}
			catch(Exception $e)
			{
				$this->add_error('api_get_local_code', $e->getMessage());
				return false;
			}

			if(!$local_code)
			{
				$this->add_error('api_get_local_code_data', $this->status_messages['could_not_obtain_local_code']);
				return false;
			}

			$this->local_code_storage->update($local_code);
		}

		return $this->validate_local_code($local_code);
	}

	/**
	 * Установка текущего экземпляра
	 *
	 * @param Tecodes_Local_Instance|null $instance
	 */
	public function set_instance($instance)
	{
		$this->instance = $instance;
	}

	/**
	 * Получение возникших ошибок
	 *
	 * @return false|array
	 */
	public function get_errors()
	{
		return $this->errors;
	}

	/**
	 * Добавление ошибки
	 *
	 * @param string $code
	 * @param string $message
	 */
	public function add_error($code = 'unknown', $message = '')
	{
		$this->errors[$code] = $message;
	}
}