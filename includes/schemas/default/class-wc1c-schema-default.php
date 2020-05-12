<?php
/**
 * Default schema class
 *
 * @package Wc1c/Schemas
 */
defined('ABSPATH') || exit;

class Wc1c_Schema_Default extends Wc1c_Abstract_Schema
{
	/**
	 * Wc1c_Schema_Logger
	 *
	 * @var null
	 */
	private $logger = null;

	/**
	 * Current time
	 *
	 * @var string
	 */
	public $time;

	/**
	 * Current import data
	 *
	 * @var array
	 */
	public $current_data = [];

	/**
	 * Main schema directory
	 *
	 * @var string
	 */
	private $upload_directory = '';

	/**
	 * Initialize
	 *
	 * @throws Exception
	 */
	public function init()
	{
		/**
		 * Init environment
		 */
		$this->init_environment();

		/**
		 * Logger
		 */
		if(false === $this->load_logger())
		{
			WC1C()->logger()->critical('init: load_logger');
			return;
		}

		$this->logger()->info('init: start');

		/**
		 * View configuration form
		 */
		if(true === is_wc1c_admin_request())
		{
			add_filter('wc1c_admin_configurations-update_form_load_fields', array($this, 'configurations_fields_auth'), 10, 1);
			add_filter('wc1c_admin_configurations-update_form_load_fields', array($this, 'configurations_fields_tech'), 10, 1);
		}

		/**
		 * Api requests handler
		 */
		if(true === is_wc1c_api_request())
		{
			add_action('wc1c_api_' . $this->get_id(), array($this, 'api_handler'), 10);
		}

		$this->logger()->debug('init: end', $this);
	}

	/**
	 * @return string
	 */
	public function get_upload_directory()
	{
		return $this->upload_directory;
	}

	/**
	 * @param string $upload_directory
	 */
	public function set_upload_directory($upload_directory)
	{
		$this->upload_directory = $upload_directory;
	}

	/**
	 * Schema environment
	 */
	private function init_environment()
	{
		$configuration_id = WC1C()->environment()->get('current_configuration_id', 0);

		$schema_directory = WC1C()->environment()->get('wc1c_upload_directory') . DIRECTORY_SEPARATOR . $this->get_id() . '_' . $configuration_id;

		$this->set_upload_directory($schema_directory);

		WC1C()->environment()->set('wc1c_current_schema_upload_directory', $this->get_upload_directory());
	}
	
	/**
	 * Load logger
	 */
	private function load_logger()
	{
		$path = $this->get_upload_directory();
		$level = $this->get_options('logger');

		try
		{
			$logger = new Wc1c_Schema_Logger($path, $level, 'main.log');
			$this->set_logger($logger);
		}
		catch(Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Configuration fields: tech
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurations_fields_tech($fields)
	{
		$fields['title_tech'] = array
		(
			'title' => __('Technical details', 'wc1c'),
			'type' => 'title',
			'description' => __('Changing data processing behavior for compatibility of the environment and other systems.', 'wc1c'),
		);

		$fields['logger'] = array
		(
			'title' => __('Logging level', 'wc1c'),
			'type' => 'select',
			'description' => __('You can enable logging, specify the level of error that you want to benefit from logging. You can send reports to developer manually by pressing the button. All sensitive data in the report are deleted. By default, the error rate should not be less than ERROR.', 'wc1c'),
			'default' => '400',
			'options' => array
			(
				'' => __('Off', 'wc1c'),
				'100' => __('DEBUG', 'wc1c'),
				'200' => __('INFO', 'wc1c'),
				'250' => __('NOTICE', 'wc1c'),
				'300' => __('WARNING', 'wc1c'),
				'400' => __('ERROR', 'wc1c'),
				'500' => __('CRITICAL', 'wc1c'),
				'550' => __('ALERT', 'wc1c'),
				'600' => __('EMERGENCY', 'wc1c')
			)
		);

		$fields['skip_file_processing'] = array
		(
			'title' => __('Skip processing of files', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Disabling the actual processing of CommerceML files. Files will be accepted, but instead of processing them, they will be skipped with successful completion of processing.', 'wc1c'),
			'default' => 'no'
		);

		$fields['convert_cp1251'] = array
		(
			'title' => __('Converting to Windows-1251', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Data from utf-8 will be converted to Windows-1251 encoding. Use this feature for compatibility with older versions of 1C.', 'wc1c'),
			'default' => 'no'
		);

		$fields['post_file_max_size'] = array
		(
			'title' => __('Maximum request size', 'wc1c'),
			'type' => 'text',
			'description' => __('Enter the maximum request size. You can only reduce the value.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 100px;',
		);

		$fields['file_zip'] = array
		(
			'title' => __('Support for data compression', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('1C can transfer files in archives to reduce the number of HTTP requests and compress data. In this case, the load may increase when unpacking archives, or even it may be impossible to unpack due to server restrictions.', 'wc1c'),
			'default' => 'no'
		);

		$fields['delete_files_after_import'] = array
		(
			'title' => __('Deleting files after processing', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If deletion is disabled, the exchange files will remain in the directories until the next exchange. Otherwise, all processed files will be deleted immediately after error-free processing.', 'wc1c'),
			'default' => 'no'
		);

		return $fields;
	}

	/**
	 * Configuration fields: auth
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurations_fields_auth($fields)
	{
		$fields['title_auth'] = array
		(
			'title' => __('Requests authorization', 'wc1c'),
			'type' => 'title',
			'description' => __('Data for authorization of requests. These settings will connect 1C.', 'wc1c'),
		);

		$fields['user_login'] = array
		(
			'title' => __('Login to connect', 'wc1c'),
			'type' => 'text',
			'description' => __('Enter the username to connect from 1C. It should be the same as when setting up in 1C.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		);

		$fields['user_password'] = array
		(
			'title' => __('Password to connect', 'wc1c'),
			'type' => 'text',
			'description' => __('Enter the users password to connect from 1C. It must be the same as when setting up in 1C.', 'wc1c'),
			'default' => '',
			'css' => 'min-width: 350px;',
		);

		return $fields;
	}

	/**
	 * Get logger
	 *
	 * @return Wc1c_Schema_Logger|null
	 */
	public function logger()
	{
		return $this->logger;
	}

	/**
	 * Set schema logger
	 *
	 * @param Wc1c_Schema_Logger|null $logger
	 */
	public function set_logger($logger)
	{
		$this->logger = $logger;
	}

	/**
	 * Возвращает максимальный объем файла в байтах для загрузки
	 *
	 * @return float|int
	 */
	private function get_post_file_size_max()
	{
		$size = wc1c_convert_size(ini_get('post_max_size'));

		$size_max_manual = wc1c_convert_size($this->get_options('post_file_max_size'));

		if($size_max_manual)
		{
			if($size_max_manual < $size)
			{
				$size = $size_max_manual;
			}
		}

		return $size;
	}

	/**
	 * Echo result
	 *
	 * @param string $type
	 * @param string $description
	 */
	private function api_response_by_type($type = 'failure', $description = '')
	{
		if($this->get_options('convert_cp1251', 'no') === 'yes' && $description !== '')
		{
			$description = mb_convert_encoding($description, 'cp1251', 'utf-8');
			header('Content-Type: text/html; charset=Windows-1251');
		}

		if($type == 'success')
		{
			echo "success\n";
		}
		else
		{
			echo "failure\n";
		}

		if($description != '')
		{
			echo $description;
		}
	}

	/**
	 * Проверка авторизации
	 *
	 * @return bool
	 */
	private function api_check_auth_key()
	{
		$cookie_name = 'wc1c_' . $this->get_id();

		if(!isset($_COOKIE[$cookie_name]))
		{
			return false;
		}

		$password = $this->get_options('user_password', '1234567890qwertyuiop');

		if($_COOKIE[$cookie_name] !== md5($password))
		{
			return false;
		}

		return true;
	}

	/**
	 * Api handler
	 */
	public function api_handler()
	{
		if(WC1C()->get_configurations('current')['instance']->get_status() !== 'active')
		{
			$this->api_response_by_type('failure', 'Конфигурация не активна.');
		}

		$mode = '';
		$type = '';

		$this->logger()->debug('api_handler $_SERVER', $_SERVER);

		if(wc1c_get_var($_GET['get_param'], '') !== '' || (wc1c_get_var($_GET['get_param?type'], '') !== ''))
		{
			$output = [];
			if(isset($_GET['get_param']))
			{
				$get_param = ltrim($_GET['get_param'], '?');
				parse_str($get_param, $output);
			}

			if(array_key_exists('mode', $output))
			{
				$mode = $output['mode'];
			}
			elseif(isset($_GET['mode']))
			{
				$mode = $_GET['mode'];
			}

			if(array_key_exists('type', $output))
			{
				$type = $output['type'];
			}
			elseif(isset($_GET['type']))
			{
				$type = $_GET['type'];
			}

			if($type == '')
			{
				$type = $_GET['get_param?type'];
			}
		}

		/**
		 * Catalog
		 */
		if($type == 'catalog' && $mode != '')
		{
			switch ($mode)
			{
				case 'checkauth':
					$this->api_check_auth();
					break;

				case 'init':
					$this->api_mode_init();
					break;

				case 'file':
					$this->api_catalog_mode_file();
					break;

				case 'import':
					$this->api_catalog_mode_import();
					break;

				default:
					$this->api_response_by_type('success');
			}
		}

		$this->api_response_by_type('success');
	}

	/**
	 * Checkauth
	 */
	private function api_check_auth()
	{
		$user_login = '';
		$user_password = '';

		if(!isset($_SERVER['PHP_AUTH_USER']))
		{
			if(isset($_SERVER["REMOTE_USER"]))
			{
				$remote_user = $_SERVER["REMOTE_USER"];

				if(isset($_SERVER["REDIRECT_REMOTE_USER"]))
				{
					$remote_user = $_SERVER["REMOTE_USER"] ? $_SERVER["REMOTE_USER"] : $_SERVER["REDIRECT_REMOTE_USER"];
				}
			}
			elseif(isset($_SERVER["REDIRECT_HTTP_AUTHORIZATION"]))
			{
				$remote_user = $_SERVER["REDIRECT_HTTP_AUTHORIZATION"];
			}

			if(isset($remote_user))
			{
				$strTmp = base64_decode(substr($remote_user, 6));

				if($strTmp)
				{
					list($user_login, $user_password) = explode(':', $strTmp);
				}
			}
			else
			{
				$this->logger()->notice('Проверьте наличие записи в файле .htaccess в корне файла после RewriteEngine On:\nRewriteCond %{HTTP:Authorization} ^(.*)\nRewriteRule ^(.*) - [E=HTTP_AUTHORIZATION:%1]');
				$this->api_response_by_type('failure', 'Не указан пользователь. Проверьте настройки сервера.');
			}
		}
		else
		{
			$user_login = $_SERVER['PHP_AUTH_USER'];
			$user_password = $_SERVER['PHP_AUTH_PW'];
		}

		if($this->get_options('user_login', '') !== '')
		{
			if($this->get_options('user_login', '') !== '' && $user_login != $this->get_options('user_login', ''))
			{
				$this->api_response_by_type('failure', 'Не верный логин');
			}

			if($this->get_options('user_password', '') !== '' && $user_password !== $this->get_options('user_password', ''))
			{
				$this->api_response_by_type('failure', 'Не верный пароль');
			}
		}

		if($user_password == '')
		{
			$user_password = '1234567890qwertyuiop';
		}

		echo "success\n";
		echo "wc1c_" . $this->get_id() . "\n";
		echo md5($user_password);
		exit;
	}

	/**
	 * Init
	 *
	 * При успешной инициализации возвращает временный файл с данными:
	 * в 1-ой строке содержится признак, разрешен ли Zip (zip=yes);
	 * во 2-ой строке содержится информация об ограничении файлов по размеру (file_limit=);
	 */
	private function api_mode_init()
	{
		/**
		 * Security
		 */
		if($this->api_check_auth_key() === false)
		{
			$this->logger()->info('api_mode_init api_check_auth_key: failure');
			$this->api_response_by_type('failure', 'Авторизация не пройдена');
		}

		$zip_support = class_exists('ZipArchive') ? true : false;

		$data[0] = "zip=no";
		if($zip_support)
		{
			$this->logger()->info('ZipArchive: yes');
			$data[0] = $this->get_options('file_zip') === 'yes' ?  "zip=yes" : "zip=no";
		}

		$manual_size = wc1c_convert_size($this->get_options('post_file_max_size'));
		$post_max_size = $this->get_post_file_size_max();

		$data[1] = "file_limit=" . $post_max_size;
		if($this->get_options('post_file_max_size') && $manual_size <= $post_max_size)
		{
			$data[1] = "file_limit=" . $manual_size;
		}

		$this->logger()->debug('api_mode_init echo', $data);

		echo $data[0] . "\n";
		echo $data[1] . "\n";
		exit;
	}

	/**
	 * Выгрузка файлов в локальный каталог
	 *
	 * @return void
	 */
	public function api_catalog_mode_file()
	{
		if($this->api_check_auth_key() === false)
		{
			$this->logger()->info('api_catalog_mode_file api_check_auth_key: failure');
			$this->api_response_by_type('failure', 'Авторизация не пройдена');
		}

		$schema_upload_dir = WC1C()->environment()->get('wc1c_current_schema_upload_directory') . '/catalog/';

		if(!is_dir($schema_upload_dir))
		{
			mkdir($schema_upload_dir, 0777, true);

			if(!is_dir($schema_upload_dir))
			{
				$this->api_response_by_type('failure', 'Невозможно создать директорию: ' . $schema_upload_dir);
			}
		}

		/**
		 * Empty filename
		 */
		if(!isset($_GET['filename']))
		{
			$this->logger()->info('Filename: is empty');
			$this->api_response_by_type('failure', 'Пришел файл без имени.');
		}

		/**
		 * Full file path
		 */
		$schema_upload_file_path = $schema_upload_dir . $_GET['filename'];

		/**
		 * Logger
		 */
		$this->logger()->info('Upload file: ' . $schema_upload_file_path);

		/**
		 *  Если изображения, готовим каталоги
		 */
		if(strpos($_GET['filename'], 'import_files') !== false)
		{
			$this->logger()->info('Upload file: clean_upload_file_tree');

			/**
			 * Чистим каталоги
			 */
			$this->clean_upload_file_tree(dirname($_GET['filename']), $schema_upload_dir);
		}

		/**
		 * Разрешена ли запись файлов
		 */
		if(!is_writable($schema_upload_dir))
		{
			$this->logger()->info('Directory: ' . $schema_upload_dir . " is not writable!");
			$this->api_response_by_type('failure', 'Невозможно записать файлы в: ' . $schema_upload_dir);
		}

		/**
		 * Получаем данные из потока ввода
		 */
		$file_data = file_get_contents('php://input');

		/**
		 * Если пришли не пустые данные
		 */
		if($file_data !== false)
		{
			/**
			 * Записываем в файл
			 */
			$file_size = file_put_contents($schema_upload_file_path, $file_data, LOCK_EX);

			/**
			 * Файл не пустой
			 */
			if($file_size)
			{
				/**
				 * Logger
				 */
				$this->logger()->info('$file_size: ' . $file_size);

				/**
				 * Назначаем права на файл
				 * todo: переписать
				 */
				@chmod($schema_upload_file_path , 0777);

				/**
				 * Если пришел архив, распаковываем
				 */
				if(strpos($_GET['filename'], '.zip') !== false)
				{
					/**
					 * Распаковываем файлы
					 */
					$xml_files_result = $this->extract_zip($schema_upload_file_path);

					/**
					 * Удаляем зип архивы
					 */
					if($this->get_options('delete_zip_files_after_import') === 'yes')
					{
						$this->logger()->info('File zip deleted: ' . $schema_upload_file_path);
						unlink($schema_upload_file_path);
					}

					/**
					 * File not extracted
					 */
					if($xml_files_result === false)
					{
						$this->logger()->info('Error extract file: ' . $schema_upload_file_path);
						$this->api_response_by_type('failure');
					}

					$this->api_response_by_type('success', 'Архив успешно принят и распакован.');
				}

				$this->logger()->info('Upload file: ' . $schema_upload_file_path . ' success');
				$this->api_response_by_type('success', 'Файл успешно принят.');
			}

			$this->logger()->error('Ошибка записи файла: ' . $schema_upload_file_path);
			$this->api_response_by_type('failure', 'Не удалось записать файл: ' . $schema_upload_file_path);
		}

		/**
		 * Logger
		 */
		$this->logger()->info('File empty: ' . $schema_upload_file_path);
		$this->api_response_by_type('failure', 'Пришли пустые данные. Повторите попытку.');
	}

	/**
	 * Catalog import
	 */
	public function api_catalog_mode_import()
	{
		if($this->api_check_auth_key() === false)
		{
			$this->logger()->error('api_catalog_mode_import api_auth_key: error');
			$this->api_response_by_type('failure', 'Авторизация не пройдена.');
		}

		$this->logger()->info('api_catalog_mode_import: start');

		/**
		 * Если не пришел файл для импорта
		 */
		if(!isset($_GET['filename']) || $_GET['filename'] === '')
		{
			$this->logger()->info('api_catalog_mode_import: filename is empty');
			$this->api_response_by_type('failure', 'Не указан файл импорта');
		}

		/**
		 * Full file path to import
		 */
		$file = WC1C()->environment()->get('wc1c_current_schema_upload_directory') . '/catalog/' . sanitize_file_name($_GET['filename']);

		/**
		 * Импортируем файл
		 */
		$result_import = $this->file_import($file);

		/**
		 * Result response
		 */
		if($result_import !== false)
		{
			$this->logger()->info('api_catalog_mode_import: end');
			$this->api_response_by_type('success', 'Импорт успешно завершен.');
		}

		$this->logger()->error('api_catalog_mode_import: end');
		$this->api_response_by_type('failure', 'Импорт завершен с ошибкой.');
	}

	/**
	 * Импорт указанного файла
	 *
	 * @param $file_path
	 *
	 * @return mixed
	 */
	private function file_import($file_path)
	{
		$this->logger()->info('file_import: start');

		$type_file = $this->file_type_detect($file_path);

		$this->logger()->info('file_import: type - ' . $type_file);

		/**
		 * Если файл нормальный
		 */
		if(is_file($file_path) && $type_file != '')
		{
			/**
			 * Устанавливаем обработку ошибок
			 */
			libxml_use_internal_errors(true);

			/**
			 * Грузим данные из файла
			 */
			$xml_data = @simplexml_load_file($file_path);

			/**
			 * Если данных нет
			 */
			if(!$xml_data)
			{
				$this->logger()->error('file_import: xml errors', libxml_get_errors());
				return false;
			}

			/**
			 * Проверяем стандарт
			 */
			$result = $this->check_cml($xml_data);
			if($result == false)
			{
				$this->logger()->error('file_import: xml version error');
				return false;
			}

			$this->logger()->info('file_import: end & true');
			return true;
		}

		$this->logger()->info('file_import: end & false');
		return false;
	}

	/**
	 * Проверка файла по стандарту
	 *
	 * @param $xml
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	private function check_cml($xml)
	{
		if($xml['ВерсияСхемы'])
		{
			$this->current_data['xml_version_schema'] = (string)$xml['ВерсияСхемы'];
			return true;
		}

		throw new Exception('check_cml: schema is not valid');
	}

	/**
	 * Определение типа файла
	 *
	 * @param $file_name
	 *
	 * @return string
	 */
	public function file_type_detect($file_name)
	{
		$types = array('import', 'offers', 'prices', 'rests', 'import_files');
		foreach($types as $type)
		{
			$pos = stripos($file_name, $type);
			if($pos !== false)
			{
				return $type;
			}
		}
		return '';
	}

	/**
	 * Распаковка ZIP архива
	 *
	 * @param $zip_file_path
	 *
	 * @return boolean|int
	 */
	public function extract_zip($zip_file_path)
	{
		/**
		 * Открываем архив
		 */
		$zip_archive = zip_open($zip_file_path);

		/**
		 * Немного переменных
		 */
		$img_files = 0;
		$error_files = 0;

		/**
		 * Если архив, распаковываем
		 */
		if(is_resource($zip_archive))
		{
			/**
			 * Logger
			 */
			$this->logger()->info('Unpack start: ' . $zip_file_path);

			/**
			 * Читаем архив
			 */
			while($zip_entry = zip_read($zip_archive))
			{
				/**
				 * Текущая позиция
				 */
				$name = zip_entry_name($zip_entry);

				/**
				 * Logger
				 */
				$this->logger()->info('Unpack file name: ' . $name);

				/**
				 * Чек на файл изображения
				 */
				$import_files = $this->file_type_detect($name);

				/**
				 * Images & other
				 */
				if($import_files == 'import_files')
				{
					$result = $this->extract_zip_image($zip_archive, $zip_entry, substr($name, $import_files));

					if($result == false)
					{
						$error_files++;
					}

					$img_files++;
				}
				/**
				 * Xml
				 */
				else
				{
					$result = $this->extract_zip_xml($zip_archive, $zip_entry, $name);

					if($result == false)
					{
						$error_files++;
					}
				}
			}

			/**
			 * Logger
			 */
			$this->logger()->info('Unpack end: ' . $zip_file_path);

			/**
			 * Закрываем архив
			 */
			zip_close($zip_archive);
		}
		else
		{
			$this->logger()->error('Zip_open error: ' . $zip_file_path);
			return false;
		}

		/**
		 * Добавляем количество картинок в лог
		 */
		if($img_files > 0)
		{
			$this->logger()->info('Unpack images count: ' . $img_files);
		}

		/**
		 * Если имелись ошибки при распаковке
		 */
		if($error_files > 0)
		{
			$this->logger()->error('Unpack error files: ' . $img_files);
			return false;
		}

		return true;
	}

	/**
	 * @param $zip_arc
	 * @param $zip_entry
	 * @param $name
	 *
	 * @return boolean
	 */
	public function extract_zip_xml($zip_arc, $zip_entry, $name)
	{
		$uploads_files_dir = WC1C()->environment()->get('wc1c_current_schema_upload_directory'). '/catalog/';

		/**
		 * Directory
		 */
		if(substr($name, -1) == "/")
		{
			if(is_dir($uploads_files_dir . $name))
			{
				$this->logger()->info('Каталог существует: ' . $name);
			}
			else
			{
				$this->logger()->info('Создан каталог: ' . $name);
				@mkdir($uploads_files_dir . $name, 0775, true);
				if(!is_dir($uploads_files_dir . $name))
				{
					return false;
				}
			}
		}
		elseif(zip_entry_open($zip_arc, $zip_entry, "r"))
		{
			/**
			 * File data
			 */
			$dump = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

			/**
			 * Файл существует
			 */
			if(file_exists($uploads_files_dir . $name))
			{
				unlink($uploads_files_dir . $name);
				$this->logger()->info('Удален старый файл: ' . $uploads_files_dir . $name);
			}

			if($fd = @fopen($uploads_files_dir . $name, "wb"))
			{
				$xmlFiles[] = $uploads_files_dir . $name;

				$this->logger()->info('Создан файл: ' . $uploads_files_dir . $name);

				fwrite($fd, $dump);
				fclose($fd);
			}
			else
			{
				$this->logger()->info('Ошибка создания и открытия на запись: ' . $uploads_files_dir . $name);
			}

			zip_entry_close($zip_entry);
		}

		return true;
	}

	/**
	 * Images extract from zip
	 *
	 * @param $zipArc
	 * @param $zip_entry
	 * @param $name
	 *
	 * @return boolean
	 */
	public function extract_zip_image($zipArc, $zip_entry, $name)
	{
		/**
		 * Extract to dir
		 */
		$import_files_dir = WC1C()->environment()->get('wc1c_current_schema_upload_directory') . '/catalog/import_files/';

		/**
		 * Dir
		 */
		if(substr($name, -1) == "/")
		{
			if(!is_dir($import_files_dir . $name))
			{
				mkdir($import_files_dir . $name, 0775, true);
				if(!is_dir($import_files_dir . $name))
				{
					return false;
				}
			}
		}
		/**
		 * File
		 */
		elseif(zip_entry_open($zipArc, $zip_entry, "r"))
		{
			/**
			 * File body
			 */
			$dump = zip_entry_read($zip_entry, zip_entry_filesize($zip_entry));

			/**
			 * Logger
			 */
			$this->logger()->info('Extract image: ' . $name);

			/**
			 * Если файл существует
			 */
			if(is_file($import_files_dir . $name))
			{
				/**
				 * Получаем размеры файлов
				 */
				$size_dump = strlen($dump);
				$size_file = filesize($import_files_dir . $name);

				/**
				 * Новое изображение имеет отличия
				 */
				if($size_dump !== $size_file)
				{
					$this->logger()->info('Файл: ' . $name . ' существует, но старый! Старый размер ' . $size_file . ', новый ' . $size_dump);

					/**
					 * Открываем старый файл
					 */
					$fd = @fopen($import_files_dir . $name, "wb");

					if($fd === false)
					{
						$this->logger()->error('Ошибка открытия файла: ' . $import_files_dir . $name);
						return false;
					}

					/**
					 * Записываем новые данные и закрываем дескриптор
					 */
					fwrite($fd, $dump);
					fclose($fd);

					$this->logger()->info('Файл: ' . $name . ' перезаписан.');
				}
			}
			else
			{
				/**
				 * PHP?
				 */
				$pos = strpos($dump, "<?php");

				if($pos !== false)
				{
					$this->logger()->error('Ошибка записи файла: ' . $import_files_dir . $name . '! Он является PHP скриптом и не будет записан!');
				}
				else
				{
					$fd = @fopen($import_files_dir . $name, "wb");

					if($fd === false)
					{
						$this->logger()->error('Ошибка открытия файла: ' . $import_files_dir . $name . ' Проверьте права доступа!');
						return false;
					}

					fwrite($fd, $dump);
					fclose($fd);

					$this->logger()->info('Создан файл: ' . $import_files_dir . $name);
				}
			}

			zip_entry_close($zip_entry);
		}

		$this->logger()->info('Распаковка изображения завершена!');

		return true;
	}

	/**
	 * Проверка дерева каталогов для загрузки файлов
	 *
	 * @param $path
	 * @param bool $current_dir
	 */
	private function clean_upload_file_tree($path, $current_dir = false)
	{
		foreach(explode('/', $path) as $name)
		{
			if(!$name)
			{
				continue;
			}
			if(file_exists($current_dir . $name))
			{
				if(is_dir($current_dir . $name))
				{
					$current_dir = $current_dir . $name . '/';
					continue;
				}
				unlink($current_dir . $name);
			}
			@mkdir($current_dir . $name);
			$current_dir = $current_dir . $name . '/';
		}
	}
}