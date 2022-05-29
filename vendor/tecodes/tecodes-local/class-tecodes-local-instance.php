<?php
/**
 * Tecodes instance class
 *
 * @package Tecodes/Local
 */
class Tecodes_Local_Instance
{
	/**
	 * Данные экземпляра
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Tecodes_Local_Instance constructor
	 */
	public function __construct()
	{
		$data = $this->access_details();
		$this->set_data($data);
	}

	/**
	 * Получение данных экземпляра
	 *
	 * @param string $key
	 *
	 * @return array|bool
	 */
	public function get_data($key = '')
	{
		if($key !== '')
		{
			if(array_key_exists($key, $this->data))
			{
				return $this->data[$key];
			}

			return  false;
		}

		return $this->data;
	}

	/**
	 * Установка данных экземпляра
	 *
	 * Возможно устанавливать данные только из класса и подклассов
	 *
	 * @param $data
	 * @param string $key
	 * @param bool $reload
	 *
	 * @return bool
	 */
	protected function set_data($data, $key = '', $reload = false)
	{
		if($key !== '')
		{
			if(array_key_exists($key, $this->data) && $reload === false)
			{
				return false;
			}

			$this->data[$key] = $data;
			return true;
		}

		$this->data = $data;
		return true;
	}

	/**
	 * Собираем массив с информацией окружения
	 *
	 * @return array
	 */
	public function access_details()
	{
		$access_details = array();

		if(function_exists('phpinfo'))
		{
			ob_start();
			phpinfo();
			$phpinfo = ob_get_contents();
			ob_end_clean();

			$list = strip_tags($phpinfo);
			$access_details['domain'] = $this->scrape_php_info($list, 'HTTP_HOST');
			$access_details['ip'] = $this->scrape_php_info($list, 'SERVER_ADDR');
			$access_details['directory'] = $this->scrape_php_info($list, 'SCRIPT_FILENAME');
			$access_details['server_hostname'] = $this->scrape_php_info($list, 'System');
			$access_details['server_ip'] = gethostbyname($access_details['server_hostname']);
		}

		// На всякий случай собираем еще данные
		$access_details['domain'] = ($access_details['domain']) ? $access_details['domain'] : $_SERVER['HTTP_HOST'];
		$access_details['ip'] = ($access_details['ip']) ? $access_details['ip'] : $this->server_addr();
		$access_details['directory'] = ($access_details['directory']) ? $access_details['directory'] : $this->path_translated();
		$access_details['server_hostname'] = ($access_details['server_hostname']) ? $access_details['server_hostname'] : gethostbyaddr($access_details['ip']);
		$access_details['server_hostname'] = ($access_details['server_hostname']) ? $access_details['server_hostname'] : 'Unknown';
		$access_details['server_ip'] = ($access_details['server_ip']) ? $access_details['server_ip'] : gethostbyaddr($access_details['ip']);
		$access_details['server_ip'] = ($access_details['server_ip']) ? $access_details['server_ip'] : 'Unknown';

		foreach($access_details as $key => $value)
		{
			$access_details[$key] = ($access_details[$key]) ? $access_details[$key] : 'Unknown';
		}

		return $access_details;
	}

	/**
	 * Определяем Windows систему
	 *
	 * @return boolean
	 */
	public function is_windows()
	{
		return (strtolower(substr(php_uname(), 0, 7)) === 'windows');
	}

	/**
	 * Проверяем на локальность сервера
	 *
	 * @return bool
	 */
	public function get_ip_local()
	{
		$local_ip = '';

		if (function_exists('phpinfo'))
		{
			ob_start();
			phpinfo();
			$phpinfo = ob_get_contents();
			ob_end_clean();

			$list = strip_tags($phpinfo);
			$local_ip = $this->scrape_php_info($list, 'SERVER_ADDR');
		}

		$local_ip = ($local_ip) ? $local_ip : $this->server_addr();

		if($local_ip === '127.0.0.1')
		{
			return true;
		}

		return false;
	}

	/**
	 * Получаем детали доступа используя phpinfo()
	 *
	 * @param string $all
	 * @param string $target
	 *
	 * @return string|boolean string при успехе; boolean при ошибке
	 */
	public function scrape_php_info($all, $target)
	{
		$all = explode($target, $all);

		if(count($all) < 2)
		{
			return false;
		}

		$all = explode("\n", $all[1]);
		$all = trim($all[0]);

		if($target === 'System')
		{
			$all = explode(" ", $all);
			$all = trim($all[(strtolower($all[0]) === 'windows' && strtolower($all[1]) === 'nt') ? 2 : 1]);
		}

		if($target === 'SCRIPT_FILENAME')
		{
			$slash = ($this->is_windows() ? '\\' : '/');

			$all = explode($slash, $all);
			array_pop($all);
			$all = implode($slash, $all);
		}

		if(substr($all, 1, 1) === ']')
		{
			return false;
		}

		return $all;
	}

	/**
	 * Получаем айпи адрес сервера
	 *
	 * @return string|boolean string при успехе; boolean при ошибке
	 */
	public function server_addr()
	{
		$options = array('SERVER_ADDR', 'LOCAL_ADDR');
		foreach($options as $key)
		{
			if(isset($_SERVER[$key]))
			{
				return $_SERVER[$key];
			}
		}
		return false;
	}

	/**
	 * Получаем путь до директории скрипта
	 *
	 * @return string|boolean string при успехе; boolean при ошибке
	 */
	public function path_translated()
	{
		$option = array
		(
			'PATH_TRANSLATED',
			'ORIG_PATH_TRANSLATED',
			'SCRIPT_FILENAME',
			'DOCUMENT_ROOT',
			'APPL_PHYSICAL_PATH'
		);

		foreach($option as $key)
		{
			if(!isset($_SERVER[$key]) || strlen(trim($_SERVER[$key])) <= 0)
			{
				continue;
			}
			if($this->is_windows() && strpos($_SERVER[$key], '\\'))
			{
				return substr($_SERVER[$key], 0, strrpos($_SERVER[$key], '\\'));
			}
			return substr($_SERVER[$key], 0, strrpos($_SERVER[$key], '/'));
		}
		return false;
	}
}