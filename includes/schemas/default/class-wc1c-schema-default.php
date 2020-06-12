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
	 * Schema level
	 *
	 * @var null|Wc1c_Schema_Logger
	 */
	private $logger = null;

	/**
	 * Import full or not
	 *
	 * @var bool
	 */
	private $import_full = true;

	/**
	 * Main schema upload directory
	 *
	 * @var string
	 */
	private $upload_directory = '';

	/**
	 * Initialize
	 *
	 * @throws Exception
	 *
	 * @return bool
	 */
	public function init()
	{
		try
		{
			$this->init_environment();
		}
		catch(Exception $e)
		{
			throw new Exception('init: - ' . $e);
		}

		if(false === $this->init_logger())
		{
			throw new Exception('init: load_logger error');
		}

		$this->set_options($this->configuration()->get_options());

		if(true === is_wc1c_admin_request())
		{
			add_filter('wc1c_admin_configurations-update_form_load_fields', array($this, 'configurations_fields_auth'), 10, 1);
			add_filter('wc1c_admin_configurations-update_form_load_fields', array($this, 'configurations_fields_processing'), 10, 1);
			add_filter('wc1c_admin_configurations-update_form_load_fields', array($this, 'configurations_fields_tech'), 10, 1);
		}

		if(true === is_wc1c_api_request())
		{
			add_action('wc1c_api_' . $this->get_id(), array($this, 'api_handler'), 10);
		}

		return true;
	}

	/**
	 * @return string
	 */
	protected function get_upload_directory()
	{
		return $this->upload_directory;
	}

	/**
	 * @param string $upload_directory
	 */
	protected function set_upload_directory($upload_directory)
	{
		$this->upload_directory = $upload_directory;
	}

	/**
	 * Schema environment
	 */
	private function init_environment()
	{
		$configuration_id = $this->configuration()->get_id();
		$upload_directory = WC1C()->environment()->get('wc1c_upload_directory');

		$schema_directory = $upload_directory . '/' . $this->get_id() . '_' . $configuration_id;

		$this->set_upload_directory($schema_directory);

		WC1C()->environment()->set('wc1c_current_schema_upload_directory', $this->get_upload_directory());
	}
	
	/**
	 * Initializing logger
	 */
	private function init_logger()
	{
		$path = $this->get_upload_directory();
		$level = $this->get_options('logger', 400);

		try
		{
			$logger = new Wc1c_Schema_Logger($path, $level, 'main.log');
		}
		catch(Exception $e)
		{
			return false;
		}

		try
		{
			$this->set_logger($logger);
		}
		catch(Exception $e)
		{
			return false;
		}

		return true;
	}

	/**
	 * Configuration fields: processing
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function configurations_fields_processing($fields)
	{
		$fields['title_processing'] = array
		(
			'title' => __('Processing details', 'wc1c'),
			'type' => 'title',
			'description' => __('Changing the behavior of the file processing.', 'wc1c'),
		);

		$fields['skip_file_processing'] = array
		(
			'title' => __('Skip processing of files', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Disabling the actual processing of CommerceML files. Files will be accepted, but instead of processing them, they will be skipped with successful completion of processing.', 'wc1c'),
			'default' => 'no'
		);

		$fields['delete_files_after_processing'] = array
		(
			'title' => __('Deleting CommerceML files after processing', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If deletion is disabled, the exchange files will remain in the directories until the next exchange. Otherwise, all processed files will be deleted immediately after error-free processing.', 'wc1c'),
			'default' => 'no'
		);

		$fields['delete_zip_files_after_extract'] = array
		(
			'title' => __('Deleting ZIP files after extract', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('If deletion is disabled, the exchange ZIP files will remain in the directories until the next exchange.', 'wc1c'),
			'default' => 'no'
		);

		return $fields;
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
			'description' => __('Changing processing behavior for compatibility of the environment and other systems.', 'wc1c'),
		);

		$logger_path = $this->logger()->get_path() . DIRECTORY_SEPARATOR . $this->logger()->get_name();

		$fields['logger'] = array
		(
			'title' => __('Logging events', 'wc1c'),
			'type' => 'select',
			'description' => __('Can enable logging, specify the level of error that to benefit from logging.
			 Can send reports to developer. All sensitive data in the report are deleted.
			  By default, the error rate should not be less than ERROR.', 'wc1c'). '<br/><b>' . __('Current file: ', 'wc1c') . '</b>' . $logger_path,
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

		$fields['convert_cp1251'] = array
		(
			'title' => __('Converting to Windows-1251', 'wc1c'),
			'type' => 'checkbox',
			'label' => __('Check the checkbox if want to enable this feature. Disabled by default.', 'wc1c'),
			'description' => __('Data from utf-8 will be converted to Windows-1251 encoding. Use this feature for compatibility with older versions of 1C.', 'wc1c'),
			'default' => 'no'
		);

		$fields['php_post_max_size'] = array
		(
			'title' => __('Maximum request size', 'wc1c'),
			'type' => 'text',
			'description' => __('The setting must not take a size larger than specified in the server settings.', 'wc1c'),
			'default' => WC1C()->environment()->get('php_post_max_size'),
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

		$url_raw = get_site_url(null, '/?wc1c-api=' . $this->configuration()->get_id() . '&get_param');
		$url_raw = '<p class="input-text p-2 bg-light regular-input wc1c_urls">' . $url_raw . '</p>';

		$fields['url_requests'] = array
		(
			'title' => __('Requests URL', 'wc1c'),
			'type' => 'raw',
			'raw' => $url_raw,
			'description' => __('This address is entered in the exchange settings on the 1C side. It will receive requests from 1C.', 'wc1c'),
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
	protected function logger()
	{
		return $this->logger;
	}

	/**
	 * Set schema logger
	 *
	 * @param Wc1c_Schema_Logger|null $logger
	 */
	protected function set_logger($logger)
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
	private function api_handler_response_by_type($type = 'failure', $description = '')
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
		exit;
	}

	/**
	 * Проверка авторизации
	 *
	 * @return bool
	 */
	private function api_handler_check_auth_key()
	{
		$cookie_name = 'wc1c_' . $this->get_id();

		if(!isset($_COOKIE[$cookie_name]))
		{
			$this->logger()->warning('api_handler_check_auth_key: $_COOKIE[$cookie_name] empty');
			return false;
		}

		$password = $this->get_options('user_password', '1234567890qwertyuiop');

		if($_COOKIE[$cookie_name] !== md5($password))
		{
			$this->logger()->warning('api_handler_check_auth_key: $_COOKIE[$cookie_name] !== md5($password)');
			return false;
		}

		return true;
	}

	/**
	 * Api handler
	 */
	public function api_handler()
	{
		$mode = '';
		$type = '';

		if(wc1c_get_var($_GET['get_param'], '') !== '' || wc1c_get_var($_GET['get_param?type'], '') !== '')
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

		$this->logger()->info('api_handler: $type=' . $type . ' $mode=' . $mode);

		/**
		 * Catalog
		 */
		if($type === 'catalog' && $mode !== '')
		{
			switch($mode)
			{
				case 'checkauth':
					$this->api_handler_check_auth();
					break;

				case 'init':
					$this->api_handler_mode_init();
					break;

				case 'file':
					$this->api_handler_catalog_mode_file();
					break;

				case 'import':
					$this->api_handler_catalog_mode_import();
					break;

				default:
					$this->api_handler_response_by_type('success');
			}
		}

		$this->api_handler_response_by_type('success');
	}

	/**
	 * Checkauth
	 */
	private function api_handler_check_auth()
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
				$this->api_handler_response_by_type('failure', __('Not specified the user. Check the server settings.', 'wc1c'));
			}
		}
		else
		{
			$user_login = $_SERVER['PHP_AUTH_USER'];
			$user_password = $_SERVER['PHP_AUTH_PW'];
		}

		if($user_login !== $this->get_options('user_login', ''))
		{
			$this->logger()->notice(__('Not a valid username', 'wc1c'));
			$this->api_handler_response_by_type('failure', __('Not a valid username', 'wc1c'));
		}

		if($user_password !== $this->get_options('user_password', ''))
		{
			$this->logger()->notice(__('Not a valid user password', 'wc1c'));
			$this->api_handler_response_by_type('failure', __('Not a valid user password', 'wc1c'));
		}

		if($user_password === '')
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
	private function api_handler_mode_init()
	{
		if($this->api_handler_check_auth_key() === false)
		{
			$this->api_handler_response_by_type('failure', __('Authorization failed', 'wc1c'));
		}

		$zip_support = false;
		if(class_exists('ZipArchive'))
		{
			$this->logger()->info('api_handler_mode_init: ZipArchive available');
			$zip_support = true;
		}

		$data[0] = 'zip=no';
		if($zip_support && $this->get_options('file_zip', 'no') === 'yes')
		{
			$data[0] = 'zip=yes';
		}

		$manual_size = wc1c_convert_size($this->get_options('post_file_max_size'));
		$post_max_size = $this->get_post_file_size_max();

		$data[1] = 'file_limit=' . $post_max_size;
		if($this->get_options('post_file_max_size') && $manual_size <= $post_max_size)
		{
			$data[1] = 'file_limit=' . $manual_size;
		}

		$this->logger()->debug('api_handler_mode_init: $data', $data);

		echo $data[0] . "\n";
		echo $data[1] . "\n";
		exit;
	}

	/**
	 * Выгрузка файлов в локальный каталог
	 *
	 * @return void
	 */
	private function api_handler_catalog_mode_file()
	{
		if($this->api_handler_check_auth_key() === false)
		{
			$this->api_handler_response_by_type('failure', __('Authorization failed', 'wc1c'));
		}

		$schema_upload_dir = $this->get_upload_directory() . '/catalog/';

		if(!is_dir($schema_upload_dir))
		{
			mkdir($schema_upload_dir, 0777, true);

			if(!is_dir($schema_upload_dir))
			{
				$this->api_handler_response_by_type('failure', __('Unable to create a directory: ', 'wc1c') . $schema_upload_dir);
			}
		}

		if(wc1c_get_var($_GET['filename'], '') === '')
		{
			$this->logger()->warning('api_handler_catalog_mode_file: filename is empty');
			$this->api_handler_response_by_type('failure', __('Filename is empty.', 'wc1c'));
		}

		$filename = wc1c_get_var($_GET['filename'], '');

		$schema_upload_file_path = $schema_upload_dir . $filename;

		$this->logger()->info('api_handler_catalog_mode_file: $schema_upload_file_path - ' . $schema_upload_file_path);

		if(strpos($filename, 'import_files') !== false)
		{
			$this->logger()->info('api_handler_catalog_mode_file: clean_upload_file_tree');
			$this->clean_upload_file_tree(dirname($filename), $schema_upload_dir);
		}

		if(!is_writable($schema_upload_dir))
		{
			$this->logger()->info('api_handler_catalog_mode_file: directory - ' . $schema_upload_dir . " is not writable!");
			$this->api_handler_response_by_type('failure', 'Невозможно записать файлы в: ' . $schema_upload_dir);
		}

		$file_data = file_get_contents('php://input');

		if($file_data !== false)
		{
			$file_size = file_put_contents($schema_upload_file_path, $file_data, LOCK_EX);

			if($file_size)
			{
				$this->logger()->info('api_handler_catalog_mode_file: $file_size - ' . $file_size);

				chmod($schema_upload_file_path , 0777);

				$this->logger()->info('api_handler_catalog_mode_file: upload file - ' . $schema_upload_file_path . ' success');

				if(strpos($filename, '.zip') !== false)
				{
					$xml_files_result = $this->extract_zip($schema_upload_file_path);

					if($this->get_options('delete_zip_files_after_extract', 'no') === 'yes')
					{
						$this->logger()->info('api_handler_catalog_mode_file: file zip deleted - ' . $schema_upload_file_path);
						unlink($schema_upload_file_path);
					}

					if($xml_files_result === false)
					{
						$this->logger()->info('api_handler_catalog_mode_file: error extract file - ' . $schema_upload_file_path);
						$this->api_handler_response_by_type('failure');
					}

					$this->api_handler_response_by_type('success', 'Архив успешно принят и распакован.');
				}

				$this->api_handler_response_by_type('success', 'Файл успешно принят.');
			}

			$this->logger()->error('api_handler_catalog_mode_file: ошибка записи файла - ' . $schema_upload_file_path);
			$this->api_handler_response_by_type('failure', 'Не удалось записать файл: ' . $schema_upload_file_path);
		}

		$this->logger()->info('api_handler_catalog_mode_file: file empty - ' . $schema_upload_file_path);
		$this->api_handler_response_by_type('failure', 'Пришли пустые данные. Повторите попытку.');
	}

	/**
	 * Catalog import
	 */
	private function api_handler_catalog_mode_import()
	{
		if($this->api_handler_check_auth_key() === false)
		{
			$this->api_handler_response_by_type('failure', __('Authorization failed', 'wc1c'));
		}

		$this->logger()->info('api_handler_catalog_mode_import: start');

		$filename = wc1c_get_var($_GET['filename']);

		if($filename === '')
		{
			$this->logger()->warning('api_handler_catalog_mode_import: filename is empty');
			$this->api_handler_response_by_type('failure', __('Import filename is empty.', 'wc1c'));
		}

		$file = $this->get_upload_directory() . '/catalog/' . sanitize_file_name($filename);

		$this->logger()->info('api_handler_catalog_mode_import: file_processing - start');

		$result_file_processing = false;

		try
		{
			$result_file_processing = $this->file_processing($file);
		}
		catch(Exception $e)
		{
			$this->logger()->error('api_handler_catalog_mode_import: exception - ' . $e->getMessage(), $e);
		}

		if($result_file_processing !== false)
		{
			if($this->get_options('delete_files_after_processing', 'no') === 'yes')
			{
				$this->logger()->info('file_import: delete file - ' . $file);
				unlink($file);
			}

			$this->logger()->info('api_handler_catalog_mode_import: success');
			$this->api_handler_response_by_type('success', 'Импорт успешно завершен.');
		}

		$this->api_handler_response_by_type('failure', 'Импорт завершен с ошибкой.');
	}

	/**
	 * CommerceML file processing
	 *
	 * @param $file_path
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function file_processing($file_path)
	{
		$type_file = $this->file_type_detect($file_path);

		$this->logger()->info('file_processing: type_file - ' . $type_file);

		if(!is_file($file_path))
		{
			throw new Exception('file_processing: $file_path is not file');
		}

		if($type_file === '')
		{
			throw new Exception('file_processing: $type_file is not valid');
		}

		if(!defined('LIBXML_VERSION'))
		{
			throw new Exception('file_processing: LIBXML_VERSION not defined');
		}

		if(!function_exists('libxml_use_internal_errors'))
		{
			throw new Exception('file_processing: libxml_use_internal_errors');
		}

		libxml_use_internal_errors(true);

		$xml_data = simplexml_load_file($file_path);

		if(!$xml_data)
		{
			$this->logger()->error('file_processing: xml errors, end & false', libxml_get_errors());
			return false;
		}

		if($this->get_options('skip_file_processing', 'yes') === 'yes')
		{
			$this->logger()->info('file_processing: skip, end & true');
			return true;
		}

		/**
		 * Классификатор
		 *
		 * cml:Классификатор
		 */
		if($xml_data->Классификатор)
		{
			$this->logger()->info('file_processing: classifier_processing start');

			try
			{
				$this->parse_xml_classifier($xml_data->Классификатор);
			}
			catch(Exception $e)
			{
				$this->logger()->error('file_processing: exception - ' . $e->getMessage());
				return false;
			}

			$this->logger()->info('file_processing: classifier_processing end');
		}

		/**
		 * Каталог
		 *
		 * cml:Каталог
		 */
		if($xml_data->Каталог)
		{
			$this->logger()->info('file_processing: catalog_processing start');

			try
			{
				$this->parse_xml_catalog($xml_data->Каталог);
			}
			catch(Exception $e)
			{
				$this->logger()->info('file_processing: exception - ' . $e->getMessage());
				return false;
			}

			$this->logger()->info('file_processing: catalog_processing end, success');
		}

		/**
		 * Предложения
		 *
		 * cml:ПакетПредложений
		 */
		if($xml_data->ПакетПредложений)
		{
			$this->logger()->info('file_processing: offers_package_processing start');

			try
			{
				$this->parse_xml_offers_package($xml_data->ПакетПредложений);
			}
			catch(Exception $e)
			{
				$this->logger()->info('file_processing: exception - ' . $e->getMessage());
				return false;
			}

			$this->logger()->info('file_processing: offers_package_processing end, success');
		}

		return true;
	}

	/**
	 * Разбор: Каталог
	 *
	 * @param $xml_catalog_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function parse_xml_catalog($xml_catalog_data)
	{
		$this->check_import_type($xml_catalog_data);

		$catalog_data['catalog_guid'] = (string) $xml_catalog_data->Ид;
		$catalog_data['classifier_guid'] = (string) $xml_catalog_data->ИдКлассификатора;
		$catalog_data['catalog_name'] = (string) $xml_catalog_data->Наименование;
		$catalog_data['catalog_description']= '';
		if($xml_catalog_data->Описание)
		{
			$catalog_data['catalog_description'] = (string) $xml_catalog_data->Описание;
		}

		$this->logger()->debug('parse_xml_catalog: $data', $catalog_data);

		if($xml_catalog_data->Товары)
		{
			try
			{
				$this->parse_xml_catalog_products($xml_catalog_data->Товары);
			}
			catch(Exception $e)
			{
				throw new Exception('parse_xml_catalog: exception - ' . $e->getMessage());
			}

			return true;
		}

		return false;
	}

	/**
	 * Обработка продуктов из <Товары>
	 *
	 * В формате CML 2.04 характеристики? названия характеристик и их значения для продуктов
	 * передаются в данном контексте.
	 *
	 * @param $xml_catalog_products_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function parse_xml_catalog_products($xml_catalog_products_data)
	{
		if(!$xml_catalog_products_data->Товар)
		{
			throw new Exception('parse_xml_catalog_products: product data empty');
		}

		foreach($xml_catalog_products_data->Товар as $xml_product_data_key => $xml_product_data)
		{
			try
			{
				$parsed_product_data = $this->parse_xml_product($xml_product_data);
			}
			catch(Exception $e)
			{
				$this->logger()->info('parse_xml_catalog_products: exception - ' . $e->getMessage());
				continue;
			}
		}

		return true;
	}

	/**
	 * @param $product_xml_data_id
	 *
	 * @return mixed
	 */
	private function parse_xml_product_id($product_xml_data_id)
	{
		$product_guid = explode("#", (string) $product_xml_data_id);
		$product_data_id['product_guid'] = $product_guid[0];
		$product_data_id['feature_guid'] = isset($product_guid[1]) ? $product_guid[1] : '';

		return $product_data_id;
	}

	/**
	 * Обработка групп продукта
	 *
	 * @param $xml_data
	 *
	 * @return array массив GUID (идентификаторов групп)
	 */
	private function parse_xml_product_groups($xml_data)
	{
		$result = [];

		foreach($xml_data->Ид as $category_guid)
		{
			/**
			 * Идентификатор группы товаров в классификаторе
			 * cml:ИдентфикаторГлобальныйТип
			 */
			$result[] = (string)$category_guid;
		}

		return $result;
	}

	/**
	 * Разбор изображений из XML в массив
	 *
	 * @throws Exception
	 *
	 * @param $xml_data
	 *
	 * @return array
	 */
	private function parse_xml_product_images($xml_data)
	{
		$images = [];

		foreach($xml_data as $image)
		{
			$image = (string)$image;

			if(empty($image))
			{
				continue;
			}

			$images[] = $image;
		}

		return $images;
	}

	/**
	 * Разбор значений свойств товара
	 *
	 * Описывает значения свойств (характеристик) номенклатурной позиции в соответствии с указанным классификатором.
	 * Если классификатор не указан, то включать данный элемент не имеет смысла.
	 *
	 * @param $xml_product_properties_values_data
	 *
	 * @return array
	 */
	private function parse_xml_product_properties_values($xml_product_properties_values_data)
	{
		$product_properties_values_data = [];

		foreach($xml_product_properties_values_data->ЗначенияСвойства as $xml_property_values_data)
		{
			/**
			 * Глобальный идентификатор
			 */
			$property_values_data['property_guid'] = (string)$xml_property_values_data->Ид;

			/**
			 * Наименование свойства
			 * может быть, а может и нет
			 *
			 * cml:НаименованиеТип
			 */
			$property_values_data['property_name'] = '';
			if($property_values_data->Наименование)
			{
				$property_values_data['property_name'] = htmlspecialchars(trim((string)$xml_property_values_data->Наименование));
			}

			/**
			 * Значение свойства
			 * Может быть значением, либо ссылкой на значение справочника классификатора.
			 */
			$property_values_data['property_value'] = htmlspecialchars(trim((string)$xml_property_values_data->Значение));

			/**
			 * Add to all
			 */
			$product_properties_values_data[$property_values_data['property_guid']] = $property_values_data;
		}

		return $product_properties_values_data;
	}

	/**
	 * Разбор характеристики с исключением дублей
	 *
	 * @throws Exception
	 *
	 * @param $xml_data
	 *
	 * @return array
	 */
	private function parse_xml_product_features($xml_data)
	{
		if(!$xml_data->ХарактеристикаТовара)
		{
			throw new Exception('parse_xml_product_features: $xml_data->ХарактеристикаТовара empty');
		}

		$features = [];

		// Уточняет характеристики поставляемого товара. Товар с разными характеристиками может иметь разную цену
		foreach($xml_data->ХарактеристикаТовара as $product_feature)
		{
			/*
			 * Идентификатор характеристики
			 *
			 * cml:НаименованиеТип
			 * 2.06+
			 */
			$id = '';
			if($product_feature->Ид)
			{
				$id = trim(htmlspecialchars((string) $product_feature->Ид));
			}

			/*
			 * Наименование характеристики
			 *
			 * cml:НаименованиеТип
			 */
			$name = trim(htmlspecialchars((string) $product_feature->Наименование));

			/*
			 * Значение характеристики
			 *
			 * cml:ЗначениеТип
			 */
			$value = trim(htmlspecialchars((string) $product_feature->Значение));

			/*
			 * Собираем без дублей в имени
			 */
			$features[$name] = array
			(
				'feature_id' => $id,
				'feature_name' => $name,
				'feature_value' => $value
			);
		}

		return $features;
	}

	/**
	 * Разбор реквизитов продукта из XML в массив данных продукта
	 *
	 * @param $xml_data
	 *
	 * @return array
	 * @throws Exception
	 */
	private function parse_xml_product_requisites($xml_data)
	{
		$requisites_data = [];

		// Определяет значение поризвольного реквизита документа
		foreach($xml_data->ЗначениеРеквизита as $requisite)
		{
			$name = trim((string)$requisite->Наименование);
			$value = trim((string)$requisite->Значение);

			$requisites_data[$name] = array
			(
				'name' => $name,
				'value' => $value
			);
		}

		return $requisites_data;
	}

	/**
	 * Разбор цены продукта
	 *
	 * @param $xml_product_price_data
	 *
	 * @return array
	 */
	private function parse_xml_product_price($xml_product_price_data)
	{
		$data_prices = array();

		foreach($xml_product_price_data->Цена as $price_data)
		{
			/*
			 * Идентификатор типа цены
			 *
			 * cml:ИдентфикаторГлобальныйТип
			 */
			$price_type_guid = (string) $price_data->ИдТипаЦены;

			/*
			 * Представление цены так, как оно отбражается в прайс-листе. Например: 10у.е./за 1000 шт
			 *
			 * cml:НаименованиеТип
			 */
			$price_presentation = $price_data->Представление ? (string) $price_data->Представление : '';

			/*
			 * Цена за единицу товара
			 *
			 * cml:СуммаТип
			 */
			$price = $price_data->ЦенаЗаЕдиницу ? (float) $price_data->ЦенаЗаЕдиницу : 0;

			/*
			 * Коэффициент
			 */
			$rate = $price_data->Коэффициент ? (float) $price_data->Коэффициент : 1;

			/*
			 * Валюта
			 * Код валюты по международному классификатору валют (ISO 4217).
			 * Если не указана, то используется валюта установленная для данного типа цен
			 *
			 * cml:ВалютаТип
			 */
			$currency = $price_data->Валюта ? (string) $price_data->Валюта : 'RUB';

			/*
			 * Минимальное количество товара в указанных единицах, для которого действует данная цена.
			 *
			 * cml:КоличествоТип
			 */
			$min_quantity = $price_data->МинКоличество ? (string) $price_data->МинКоличество : '0';

			/*
			 * todo: обрабатывать правильно
			 * cml:ЕдиницаИзмерения
			 */

			/**
			 * Собираем итог
			 */
			$data_prices[$price_type_guid] = array
			(
				'price' => $price,
				'price_type_guid' => $price_type_guid,
				'price_rate' => $rate,
				'price_currency' => $currency,
				'price_presentation' => $price_presentation,
				'min_quantity' => $min_quantity,
			);
		}

		return $data_prices;
	}

	/**
	 * Разбор остатков продукта
	 *
	 * @param $xml_data
	 *
	 * @return float|int
	 */
	private function parse_xml_product_quantity($xml_data)
	{
		$quantity = 0;

		/*
		 * CML < 2.08
		 */
		if($xml_data->Количество)
		{
			$quantity = (float)$xml_data->Количество;
		}
		elseif($xml_data->Склад)
		{
			foreach ($xml_data->Склад as $product_quantity)
			{
				$quantity += (float)$product_quantity['КоличествоНаСкладе'];
			}
		}

		/*
		 * CML 2.09, 2.10
		 */
		if($xml_data->Остатки)
		{
			foreach($xml_data->Остатки->Остаток as $product_quantity)
			{
				// Если нет складов или общий остаток предложения
				if($xml_data->Остаток->Количество)
				{
					$quantity = (float)$product_quantity->Количество;
				}
				elseif($product_quantity->Склад)
				{
					foreach($product_quantity->Склад as $quantity_warehouse)
					{
						$quantity += (float)$quantity_warehouse->Количество;
					}
				}
			}
		}

		return $quantity;
	}

	/**
	 * Разбор одной позиции продукта
	 *
	 * @param $xml_product_data
	 *
	 * @return array
	 * @throws Exception
	 */
	private function parse_xml_product($xml_product_data)
	{
		if(!$xml_product_data->Ид)
		{
			throw new Exception('parse_xml_product: $product_xml_data->Ид empty');
		}

		$product_data = $this->parse_xml_product_id($xml_product_data->Ид);

		/***************************************************************************************************************************************
		 * Базовые данные
		 *------------------------------------------------------------------------------------------------------------------------------------*/

		/**
		 * Штрихкод
		 */
		$product_data['ean'] = $xml_product_data->Штрихкод ? trim((string)$xml_product_data->Штрихкод) : '';
		if($product_data['ean'] === '')
		{
			$product_data['ean'] = $xml_product_data->ШтрихКод ? trim((string)$xml_product_data->ШтрихКод) : '';
		}

		/**
		 * Артикул
		 */
		$product_data['sku'] = $xml_product_data->Артикул ? htmlspecialchars(trim((string)$xml_product_data->Артикул)) : '';

		/**
		 * Наименование товара
		 */
		$product_data['name'] = htmlspecialchars(trim((string)$xml_product_data->Наименование));

		/**
		 * Идентификатор товара у контрагента (идентификатор товара в системе контрагента)
		 * cml:ИдентификаторГлобальныйТип
		 */
		$product_data['product_guid_contractor'] = '';
		if($xml_product_data->ИдТовараУКонтрагента)
		{
			$product_data['product_guid_contractor'] = (string)$xml_product_data->ИдТовараУКонтрагента;
		}

		/**
		 * Категории товара
		 *
		 * Содержит идентификаторы групп, которым принадлежит данный товар в указанном классификаторе.
		 * Если классификатор не указан, то включать данный элемент не имеет смысла.
		 */
		$product_data['categories'] = [];
		if($xml_product_data->Группы)
		{
			try
			{
				$product_data['categories'] = $this->parse_xml_product_groups($xml_product_data->Группы);
			}
			catch(Exception $e){}
		}

		/**
		 * Описание товара
		 */
		$product_data['description'] = '';
		if($xml_product_data->Описание)
		{
			$description = htmlspecialchars(trim((string)$xml_product_data->Описание));
			$product_data['description'] = str_replace(array("\r\n", "\r", "\n"), "<br />", $description);
		}

		/**
		 * Изображения
		 *
		 * Имя файла картинки для номенклатурной позиции. Файлы картинок могут поставляться отдельно
		 * от передаваемого файла с коммерческой информацией
		 */
		$product_data['images'] = [];
		if($xml_product_data->Картинка)
		{
			try
			{
				$product_data['images'] = $this->parse_xml_product_images($xml_product_data->Картинка);
			}
			catch(Exception $e){}
		}
		// CML 2.04
		if($xml_product_data->ОсновнаяКартинка)
		{
			try
			{
				$product_data['images'] = $this->parse_xml_product_images($xml_product_data->ОсновнаяКартинка);
			}
			catch(Exception $e){}
		}

		/***************************************************************************************************************************************
		 * Дополнительные данные
		 *------------------------------------------------------------------------------------------------------------------------------------*/

		/**
		 * Страна
		 */
		$product_data['country'] = '';
		if($xml_product_data->Страна)
		{
			$product_data['country'] = htmlspecialchars(trim((string)$xml_product_data->Страна));
		}

		/**
		 * Торговая марка
		 */
		$product_data['trademark'] = '';
		if($xml_product_data->ТорговаяМарка)
		{
			$product_data['trademark'] = htmlspecialchars(trim((string)$xml_product_data->ТорговаяМарка));
		}

		/*
		 * Производитель todo: вынести разбор в отдельный метод и добавить try catch
		 *
		 * Содержит описание страны, непосредственно изготовителя и торговой марки товара.
		 * Страна - строка
		 * ТорговаяМарка - строка
		 * ВладелецТорговойМарки - Контрагент
		 * Изготовитель - Контрагент
		 */
		$product_data['manufacturer'] = [];
		if($xml_product_data->Изготовитель)
		{
			$product_data['manufacturer']['name'] = trim((string)$xml_product_data->Изготовитель->Наименование);
			$product_data['manufacturer']['name_guid'] = trim((string)$xml_product_data->Изготовитель->Ид);
		}
		elseif($xml_product_data->Производитель)
		{
			$product_data['manufacturer']['name'] = trim((string)$xml_product_data->Производитель);
		}

		/**
		 * Значения свойств
		 *
		 * Описывает значения свойств (характеристик) номенклатурной позиции в соответствии с указанным классификатором.
		 * Если классификатор не указан, то включать данный элемент не имеет смысла.
		 */
		$product_data['properties_values'] = [];
		if($xml_product_data->ЗначенияСвойств)
		{
			try
			{
				$product_data['properties_values'] = $this->parse_xml_product_properties_values($xml_product_data->ЗначенияСвойств);
			}
			catch(Exception $e){}
		}

		/**
		 * Характеристики товара
		 * Уточняет характеристики поставляемого товара. Товар с разными характеристиками может иметь разную цену.
		 */
		$product_data['product_features'] = [];
		if($xml_product_data->ХарактеристикиТовара)
		{
			try
			{
				$product_data['product_features'] = $this->parse_xml_product_features($xml_product_data->ХарактеристикиТовара);
			}
			catch(Exception $e){}
		}

		/**
		 * Значения реквизитов товара
		 * Определяет значение поризвольного реквизита документа
		 */
		$requisites_values = false;
		$product_data['requisites_values'] = [];
		if($xml_product_data->ЗначениеРеквизита) // cml 2.05-
		{
			$requisites_values = $xml_product_data->ЗначениеРеквизита;
		}
		elseif($xml_product_data->ЗначенияРеквизитов) // cml 2.05+
		{
			$requisites_values = $xml_product_data->ЗначенияРеквизитов;
		}
		if($requisites_values)
		{
			try
			{
				$product_data['requisites_values'] = $this->parse_xml_product_requisites($requisites_values);
			}
			catch(Exception $e){}
		}

		/***************************************************************************************************************************************
		 * Предложения
		 *------------------------------------------------------------------------------------------------------------------------------------*/

		/**
		 * Цены
		 */
		$product_data['prices'] = [];
		if($xml_product_data->Цены)
		{
			$product_data['prices'] = $this->parse_xml_product_price($xml_product_data->Цены);
		}

		/**
		 * Количество
		 * Количество предлагаемого товара. Например, может быть указан остаток на складе.
		 */
		$product_data['quantity'] = 0;
		if($xml_product_data->Остатки || $xml_product_data->Количество || $xml_product_data->Склад)
		{
			$product_data['quantity'] = $this->parse_xml_product_quantity($xml_product_data);
		}

		/***************************************************************************************************************************************
		 * Прочие данные
		 *------------------------------------------------------------------------------------------------------------------------------------*/

		/**
		 * Полное наименование
		 */
		$product_data['full_name'] = '';
		if($xml_product_data->ПолноеНаименование)
		{
			$product_data['full_name'] = htmlspecialchars(trim((string)$xml_product_data->ПолноеНаименование));
		}
		if(isset($product_data['requisites_values']['Полное наименование']))
		{
			$product_data['full_name'] = $product_data['requisites_values']['Полное наименование']['value'];
		}

		/**
		 * Модель
		 */
		$product_data['model'] = '';
		if($xml_product_data->Модель)
		{
			$product_data['model'] = htmlspecialchars(trim((string)$xml_product_data->Модель));
		}

		/***************************************************************************************************************************************
		 * Технические данные
		 *------------------------------------------------------------------------------------------------------------------------------------*/

		/**
		 * Версия продукта
		 */
		$product_data['version'] = '';
		if($xml_product_data->НомерВерсии)
		{
			$product_data['version'] = (string)$xml_product_data->НомерВерсии;
		}

		/**
		 * Пометка товара на удаление
		 */
		$product_data['delete'] = 'no';
		if($xml_product_data->ПометкаУдаления)
		{
			$product_data['delete'] = trim((string)$xml_product_data->ПометкаУдаления) == 'true' ? 'yes' : 'no';
		}

		/**
		 * УНФ
		 */
		if($xml_product_data->Статус)
		{
			$product_data['delete'] = trim((string)$xml_product_data->Статус) == 'Удален' ? 'yes' : 'no';
		}

		/**
		 * Code из 1С?
		 */
		$product_data['code'] = '';

		return $product_data;
	}

	/**
	 * Разбор: Пакет предложений
	 *
	 * @param $xml_offers_package_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function parse_xml_offers_package($xml_offers_package_data)
	{
		$this->check_import_type($xml_offers_package_data);

		$offers_package_data['offers_package_name'] = (string) $xml_offers_package_data->Наименование;
		$offers_package_data['offers_package_guid'] = (string) $xml_offers_package_data->Ид;
		$offers_package_data['catalog_guid'] = (string) $xml_offers_package_data->ИдКаталога;
		$offers_package_data['classifier_guid'] = (string) $xml_offers_package_data->ИдКлассификатора;

		$offers_package_data['offers_package_description']= '';
		if($xml_offers_package_data->Описание)
		{
			$offers_package_data['offers_package_description'] = (string)$xml_offers_package_data->Описание;
		}

		if($xml_offers_package_data->Предложения)
		{
			$this->logger()->info('parse_xml_offers_package: $xml_data->Предложения start');

			try
			{
				$this->parse_xml_offers($xml_offers_package_data->Предложения);
			}
			catch(Exception $e)
			{
				$this->logger()->info('parse_xml_offers_package: $xml_data->Предложения exception - ' . $e->getMessage());
			}

			$this->logger()->info('parse_xml_offers_package: $xml_data->Предложения end');
		}

		return true;
	}

	/**
	 * Разбор предложений
	 *
	 * @param $xml_data
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function parse_xml_offers($xml_data)
	{
		if(!$xml_data->Предложение)
		{
			throw new Exception('parse_xml_offers: $xml_data->Предложение is not valid');
		}

		foreach($xml_data->Предложение as $xml_data_offer)
		{
			try
			{
				$parsed_product_data_offer = $this->parse_xml_product($xml_data_offer);
			}
			catch(Exception $e)
			{
				continue;
			}
		}

		return true;
	}

	/**
	 * Определение типа файла
	 *
	 * @param $file_name
	 *
	 * @return string
	 */
	private function file_type_detect($file_name)
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
	 * Проверка на наличие полной выгрузки в каталоге или в предложениях
	 *
	 * @param $xml_data
	 *
	 * @return boolean
	 */
	private function check_import_type($xml_data)
	{
		$type = true;

		if($xml_data['СодержитТолькоИзменения'])
		{
			$type = (string)$xml_data['СодержитТолькоИзменения'] == "false" ? true : false;
		}
		elseif($xml_data->СодержитТолькоИзменения)
		{
			$type = (string)$xml_data->СодержитТолькоИзменения == "false" ? true : false;
		}

		$this->set_import_full($type);

		return $this->is_import_full();
	}

	/**
	 * Маркер полного или частичного импорта
	 *
	 * @return bool
	 */
	private function is_import_full()
	{
		return $this->import_full;
	}

	/**
	 * Установка полного или частичного импорта
	 *
	 * @param bool $import_full
	 */
	public function set_import_full($import_full)
	{
		$this->import_full = $import_full;
	}

	/**
	 * Парсинг групп из классификатора
	 *
	 * @throws Exception
	 *
	 * @param $xml_data
	 * @param string $parent_id
	 * @param array $groups
	 *
	 * @return array - все категории найденные в классификаторе
	 */
	private function parse_xml_classifier_groups($xml_data, $parent_id = '', &$groups = [])
	{
		foreach($xml_data->Группа as $xml_category)
		{
			$id = (string) $xml_category->Ид;

			try
			{
				$groups[$id] = $this->parse_xml_group($xml_category, $parent_id);
			}
			catch(Exception $e)
			{
				$this->logger()->warning('parse_xml_classifier_groups: ' . $e->getMessage());
				continue;
			}

			if($xml_category->Группы)
			{
				$this->parse_xml_classifier_groups($xml_category->Группы, $id, $groups);
			}
		}

		return $groups;
	}

	/**
	 * @param $xml_group
	 * @param string $parent_guid
	 *
	 * @return array|mixed|void
	 * @throws Exception
	 */
	private function parse_xml_group($xml_group, $parent_guid = '')
	{
		$category_guid = (string) $xml_group->Ид;
		$category_name = (string) $xml_group->Наименование;

		if($category_guid === '')
		{
			throw new Exception('parse_xml_group: $category_guid not valid');
		}
		if($category_name === '')
		{
			throw new Exception('parse_xml_group: $category_name not valid');
		}

		$data = array
		(
			'category_guid' => $category_guid,
			'category_name' => $category_name,
			'category_parent_guid' => $parent_guid,
			'category_version' => $xml_group->НомерВерсии ? (string) $xml_group->НомерВерсии : ''
		);

		if($xml_group->Картинка)
		{
			$data['category_image'] = (string) $xml_group->Картинка;
		}

		if((string)$xml_group->ПометкаУдаления === 'true')
		{
			$data['category_mark_delete'] = 'yes';
		}

		$data = apply_filters($this->get_schema_prefix() . '_parse_xml_group', $data);

		return $data;
	}

	/**
	 * Обновление данных о группах в классификаторе
	 *
	 * @param $classifier_groups
	 *
	 * @return bool
	 */
	public function update_classifier_groups($classifier_groups = [])
	{
		return update_option($this->get_prefix() . '_classifier_groups', $classifier_groups, 'no');
	}

	/**
	 * @return array
	 */
	public function load_relationship_categories()
	{
		return get_option($this->get_prefix() . '_relationship_cats', []);
	}

	/**
	 * Загрузка текущих категорий в WooCommerce
	 *
	 * @throws
	 *
	 * @return array
	 */
	private function load_shop_categories()
	{
		$cat_args = array
		(
			'orderby'    => 'name',
			'order'      => 'asc',
			'hide_empty' => false,
		);

		$current_categories_query = get_terms('product_cat', $cat_args);

		if(is_wp_error($current_categories_query))
		{
			throw new Exception('load_categories_wc: end, get_terms error');
		}

		$this->logger()->debug('load_categories_wc: get_terms $current_categories_query', $current_categories_query);

		if(is_array($current_categories_query))
		{
			$categories = [];

			foreach($current_categories_query as $row => $value)
			{
				$categories[$value->term_id] = array
				(
					'category_id' => $value->term_id,
					'category_name' => $value->name,
					'category_slug' => $value->slug,
					'category_parent_id' => $value->parent,
					'category_description' => $value->description,
					'category_product_count' => $value->count,
					'category_taxonomy_id' => $value->term_taxonomy_id,
				);
			}

			return $categories;
		}

		throw new Exception('load_categories_wc: end, $current_categories_query type error');
	}

	/**
	 * Обновление связи категории 1С с категорией WooCommerce
	 *
	 * @param $relationship_categories
	 *
	 * @return bool
	 */
	public function update_relationship_categories($relationship_categories)
	{
		return update_option($this->get_prefix() . '_relationship_cats', $relationship_categories, 'no');
	}

	/**
	 * Рекурсивный поиск в массиве
	 *
	 * @param $needle
	 * @param $haystack
	 *
	 * @return bool|int|string
	 */
	private function recursive_array_search($needle,$haystack)
	{
		foreach($haystack as $key => $value)
		{
			$current_key = $key;

			if($needle === $value OR (is_array($value) && $this->recursive_array_search($needle, $value) !== false))
			{
				return $current_key;
			}
		}

		return false;
	}

	/**
	 * Добавление категории
	 *
	 * @throws Exception
	 *
	 * @param $data
	 *
	 * @return int|false
	 */
	private function add_category($data)
	{
		if(!array_key_exists('category_slug', $data))
		{
			$data['category_slug'] = '';
		}
		if(!array_key_exists('category_description', $data))
		{
			$data['category_description'] = '';
		}
		if(!array_key_exists('category_parent_id', $data))
		{
			$data['category_parent_id'] = 0;
		}

		$category_result = wp_insert_term
		(
			$data['category_name'],
			'product_cat',
			array
			(
				'description' => $data['category_description'],
				'slug' => $data['category_slug'],
				'parent' => (int) $data['category_parent_id']
			)
		);

		if(is_wp_error($category_result))
		{
			throw new Exception('add_category: wp_error - ' . $category_result->get_error_message());
		}

		return isset($category_result['term_id']) ? $category_result['term_id'] : false;
	}

	/**
	 * Обработка групп полученных из классификатора
	 *
	 * @param array $classifier_groups
	 *
	 *@throws Exception
	 *
	 * @return bool
	 */
	private function processing_classifier_groups($classifier_groups = [])
	{
		if($this->is_import_full() !== true)
		{
			return true;
		}

		if(empty($classifier_groups))
		{
			return true;
		}

		$classifier_groups = apply_filters($this->get_schema_prefix() . '_processing_classifier_groups', $classifier_groups);

		if(!is_array($classifier_groups))
		{
			throw new Exception('processing_classifier_groups: $groups is not valid');
		}

		try
		{
			$this->update_classifier_groups($classifier_groups);
		}
		catch(Exception $e)
		{
			throw new Exception('processing_classifier_groups: exception - ' . $e->getMessage());
		}

		try
		{
			$relationship_categories = $this->load_relationship_categories();
		}
		catch(Exception $e)
		{
			throw new Exception('processing_classifier_groups: exception - ' . $e->getMessage());
		}

		try
		{
			$shop_categories = $this->load_shop_categories();
		}
		catch(Exception $e)
		{
			throw new Exception('processing_classifier_groups: exception - ' . $e->getMessage());
		}

		foreach($classifier_groups as $classifier_group => $classifier_group_value)
		{
			if(!is_array($classifier_group_value))
			{
				continue;
			}

			if(isset($relationship_categories[$classifier_group_value['category_guid']]))
			{
				$shop_category_id = $relationship_categories[$classifier_group_value['category_guid']];

				if(!isset($shop_categories[$shop_category_id]))
				{
					unset($relationship_categories[$classifier_group_value['category_guid']]);
				}
				else
				{
					/**
					 * Обновление имени
					 */
					// todo

					/**
					 * Обновление описания
					 */
					// todo
				}
			}

			$check_category = $this->recursive_array_search($classifier_group_value['category_name'], $shop_categories);

			if(false !== $check_category)
			{
				continue;
			}

			$category_data = array
			(
				'category_name' => $classifier_group_value['category_name'],
				'category_description' => '',
				'category_slug' => '',
				'category_parent_id' => 0,
			);

			if($classifier_group_value['category_parent_guid'] !== '' && isset($relationship_categories[$classifier_group_value['category_parent_guid']]))
			{
				$category_data['category_parent_id'] = (int)$relationship_categories[$classifier_group_value['category_parent_guid']];
			}

			try
			{
				$category_id = $this->add_category($category_data);
			}
			catch(Exception $e)
			{
				$this->logger()->info('processing_classifier_groups: exception - ' . $e->getMessage());
				continue;
			}

			$relationship_categories[$classifier_group_value['category_guid']] = $category_id;

			try
			{
				$this->update_relationship_categories($relationship_categories);
			}
			catch(Exception $e)
			{
				$this->logger()->info('processing_classifier_groups: exception - ' . $e->getMessage());
				continue;
			}
		}

		return true;
	}

	/**
	 * Обработка свойств классификатора
	 *
	 * @param $xml_data
	 *
	 * @return array
	 */
	private function parse_xml_classifier_properties($xml_data)
	{
		if($xml_data->Свойство)
		{
			$properties_xml_data = $xml_data->Свойство;
		}
		else
		{
			$properties_xml_data = $xml_data->СвойствоНоменклатуры;
		}

		$classifier_properties = [];

		foreach($properties_xml_data as $property_xml_data)
		{
			try
			{
				$property_data = $this->parse_xml_property($property_xml_data);
				$classifier_properties[$property_data['property_guid']] = $property_data;
			}
			catch(Exception $e)
			{
				$this->logger()->info('parse_xml_classifier_properties: exception - ' . $e->getMessage());
			}
		}

		return $classifier_properties;
	}

	/**
	 * Свойство
	 *
	 * @param $xml_property
	 *
	 * @return array
	 */
	private function parse_xml_property($xml_property)
	{
		/**
		 * Идентификатор свойства в классификаторе
		 */
		$property_data['property_guid'] = (string)$xml_property->Ид;

		/**
		 * Наименование свойства в классификаторе
		 */
		$property_data['property_name'] = htmlspecialchars(trim((string) $xml_property->Наименование));

		/**
		 * Описание свойства, например, для чего оно предназначено
		 */
		$property_data['property_description'] = htmlspecialchars(trim((string) $xml_property->Описание));

		/**
		 * Обязательное
		 */
		$property_data['property_required']  = 'no';
		if($xml_property->Обязательное)
		{
			$property_data['property_required'] = (string)$xml_property->Обязательное == 'true' ? 'yes' : 'no';
		}

		/**
		 * Множественное
		 */
		$property_data['property_multiple']  = 'no';
		if($xml_property->Множественное)
		{
			$property_data['property_multiple'] = (string)$xml_property->Множественное == 'true' ? 'yes' : 'no';
		}

		/**
		 * Тип значений
		 *
		 * Один из следующих типов: Строка (по умолчанию), Число,  ДатаВремя, Справочник
		 */
		$property_data['property_values_type']  = 'Строка';
		if($xml_property->ТипЗначений)
		{
			$property_data['property_values_type'] = (string)$xml_property->ТипЗначений;
		}

		/**
		 * Варианты значений
		 *
		 * Содержит коллекцию вариантов значений свойства.
		 * Если варианты указаны, то при указании  значений данного свойства для товаров должны использоваться значения СТРОГО из данного списка
		 */
		$property_values_data = []; // todo: что если не справочник?
		$property_data['property_values_variants'] = $property_values_data;
		if($property_data['property_values_type'] === 'Справочник' && $xml_property->ВариантыЗначений->Справочник)
		{
			foreach($xml_property->ВариантыЗначений->Справочник as $value)
			{
				$property_values_data[(string)$value->ИдЗначения] = htmlspecialchars(trim((string)$value->Значение));
			}

			$property_data['property_values_variants'] = $property_values_data;
		}

		/**
		 * Свойство для товаров
		 *
		 * Свойство может (или должно) использоваться при описании товаров в каталоге, пакете предложений, документах
		 */
		$property_data['property_use_products'] = 'no';
		if($xml_property->ДляТоваров)
		{
			$property_data['property_use_products'] = (string) $xml_property->ДляТоваров == 'true' ? 'yes' : 'no';
		}

		/**
		 * Для предложений
		 *
		 * Свойство может (должно) использоваться при описании товара в пакете предложений. Например: гарантийный срок, способ доставки
		 */
		$property_data['property_use_offers'] = 'no';
		if($xml_property->ДляПредложений)
		{
			$property_data['property_use_offers'] = (string) $xml_property->ДляПредложений == 'true' ? 'yes' : 'no';
		}

		/**
		 * Для документов
		 *
		 * Свойство может (должно) использоваться при описании товара в документе. Например: серийный номер
		 */
		if($xml_property->ДляДокументов)
		{
			$property_data['property_use_documents'] = (string)$xml_property->ДляПредложений == 'true' ? 'yes' : 'no';
		}

		/**
		 * Внешний
		 */
		$property_data['property_external']  = 'no';
		if($xml_property->Внешний)
		{
			$property_data['property_external'] = (string)$xml_property->Внешний == 'true' ? 'yes' : 'no';
		}

		/**
		 * Информационное
		 */
		$property_data['property_informational']  = 'no';
		if($xml_property->Информационное)
		{
			$property_data['property_informational'] = (string)$xml_property->Информационное == 'true' ? 'yes' : 'no';
		}

		/**
		 * Маркер удаления
		 */
		$property_data['property_mark_delete']  = 'no';
		if($xml_property->ПометкаУдаления)
		{
			$property_data['property_mark_delete'] = (string)$xml_property->ПометкаУдаления == 'true' ? 'yes' : 'no';
		}

		/**
		 * Номер версии
		 */
		$property_data['property_version']  = '';
		if($xml_property->НомерВерсии)
		{
			$property_data['property_version'] = (string)$xml_property->НомерВерсии;
		}

		return $property_data;
	}

	/**
	 * Разбор: Классификатор
	 *
	 * @param $xml_classifier_data
	 *
	 * @return array|bool
	 * @throws Exception
	 */
	private function parse_xml_classifier($xml_classifier_data)
	{
		$this->check_import_type($xml_classifier_data);

		$classifier_data['classifier_guid'] = (string)$xml_classifier_data->Ид;
		$classifier_data['classifier_name'] = (string)$xml_classifier_data->Наименование;

		$this->logger()->debug('parse_xml_classifier: $data ', $classifier_data);

		/**
		 * Группы
		 * Определяет иерархическую структуру групп номенклатуры
		 *
		 * cml:Группа
		 */
		if($xml_classifier_data->Группы)
		{
			$this->logger()->info('parse_xml_classifier: classifier_processing_groups start');

			try
			{
				$classifier_groups = $this->parse_xml_classifier_groups($xml_classifier_data->Группы);
			}
			catch(Exception $e)
			{
				throw new Exception('parse_xml_classifier: exception - ' . $e->getMessage());
			}

			try
			{
				$this->processing_classifier_groups($classifier_groups);
			}
			catch(Exception $e)
			{
				throw new Exception('parse_xml_classifier: exception - ' . $e->getMessage());
			}

			$this->logger()->info('parse_xml_classifier: classifier_processing_groups end, success');
		}

		/**
		 * Свойства
		 * Содержит коллекцию свойств, значения которых можно или нужно указать ДЛЯ ВСЕХ товаров в
		 * каталоге, пакете предложений, документах
		 *
		 * cml:Свойство
		 */
		if($xml_classifier_data->Свойства)
		{
			$this->logger()->info('parse_xml_classifier: classifier_processing_properties start');

			try
			{
				$classifier_properties = $this->parse_xml_classifier_properties($xml_classifier_data->Свойства);
			}
			catch(Exception $e)
			{
				throw new Exception('parse_xml_classifier: exception - ' . $e->getMessage());
			}

			try
			{
				$this->processing_classifier_properties($classifier_properties);
			}
			catch(Exception $e)
			{
				throw new Exception('parse_xml_classifier: exception - ' . $e->getMessage());
			}

			$this->logger()->info('parse_xml_classifier: classifier_processing_properties end, success');
		}

		return $classifier_data;
	}

	/**
	 * Обработка свойств классификатора
	 *
	 * @param array $classifier_properties
	 *
	 * @return bool
	 * @throws Exception
	 */
	private function processing_classifier_properties($classifier_properties = [])
	{
		if(!is_array($classifier_properties))
		{
			throw new Exception('processing_classifier_properties: $classifier_properties is not array');
		}

		if(sizeof($classifier_properties) < 1)
		{
			return true;
		}

		$this->update_classifier_properties($classifier_properties);

		try
		{
			$this->set_attributes_by_classifier_properties($classifier_properties);
		}
		catch(Exception $e)
		{
			throw new Exception('processing_classifier_properties: exception - ' . $e->getMessage());
		}

		return true;
	}

	/**
	 * @return array
	 */
	public function load_relationship_attributes_by_classifier_properties()
	{
		return get_option($this->get_prefix() . '_relationship_atts_cl_pr', []);
	}

	/**
	 * Получение идентификатора таксономии атрибута по описанию
	 *
	 * @param $label
	 *
	 * @return int
	 */
	private function get_attribute_taxonomy_id_by_label($label)
	{
		$taxonomies = wp_list_pluck(wc_get_attribute_taxonomies(), 'attribute_id', 'attribute_label');

		return isset($taxonomies[$label]) ? (int) $taxonomies[$label] : 0;
	}

	/**
	 * Назначение общих атрибутов из свойств классификатора
	 *
	 * @param array $classifier_properties
	 *
	 * @throws Exception
	 */
	private function set_attributes_by_classifier_properties($classifier_properties = [])
	{
		$relationship_attributes_by_classifier_properties = $this->load_relationship_attributes_by_classifier_properties();

		foreach($classifier_properties as $classifier_property_id => $classifier_property)
		{
			$attribute_id = 0;

			if(isset($relationship_attributes_by_classifier_properties[$classifier_property['property_guid']]))
			{
				$attribute_id = $relationship_attributes_by_classifier_properties[$classifier_property['property_guid']];
			}

			if($attribute_id === 0)
			{
				$attribute_id = $this->get_attribute_taxonomy_id_by_label($classifier_property['property_name']);
				$relationship_attributes_by_classifier_properties[$classifier_property_id] = $attribute_id;
			}

			if($attribute_id === 0)
			{
				$attribute_data =
				[
					'name' => $classifier_property['property_name']
				];

				try
				{
					$attribute_id = $this->add_attribute($attribute_data);
				}
				catch(Exception $e)
				{
					$this->logger()->warning('set_attributes_by_classifier_properties: exception - ' . $e->getMessage());
					continue;
				}

				$relationship_attributes_by_classifier_properties[$classifier_property_id] = $attribute_id;
			}
			else
			{
				// обновляем название атрибута в WooCommerce
			}

			if($attribute_id !== 0)
			{
				$property_taxonomy = $this->get_attribute_name_by_label($classifier_property['property_name']);

				if($classifier_property['property_values_type'] === 'Справочник')
				{
					foreach($classifier_property['property_values_variants'] as $property_values_variant_key => $property_values_variant_value)
					{
						try
						{
							$property_values_variant_value_term_id = $this->add_attribute_value($property_values_variant_value, 'pa_' . $property_taxonomy);
						}
						catch(Exception $e)
						{
							$this->logger()->warning('parse_xml_classifier_properties: exception - ' . $e->getMessage());
						}

						//todo: save $property_values_variant_value_term_id
					}
				}
			}
		}

		$this->update_relationship_attributes_by_classifier_properties($relationship_attributes_by_classifier_properties);
	}

	/**
	 * Добавление общего атрибута
	 *
	 * @param $data
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function add_attribute($data)
	{
		$attribute_id = $this->get_attribute_taxonomy_id_by_label($data['name']);

		if($attribute_id !== 0)
		{
			$taxonomy_name = wc_attribute_taxonomy_name(sanitize_title($this->get_attribute_name_by_label($data['name'])));
			throw new Exception('add_attribute: exists - ' . $attribute_id . ' taxonomy=' . $taxonomy_name);
		}

		$attribute_slug = wc_sanitize_taxonomy_name(sanitize_title($data['name']));
		$max_length = 25;
		while(strlen($attribute_slug) > $max_length)
		{
			$attribute_slug = mb_substr($attribute_slug, 0, mb_strlen($attribute_slug) - 1);
		}

		$args = array
		(
			'name' => $data['name'],
			'slug' => $attribute_slug,
			'type' => 'select',
			'order_by' => 'menu_order',
			'has_archives' => false,
		);

		$attribute_id = wc_create_attribute($args); // todo: woocommerce 3.2

		if(is_wp_error($attribute_id))
		{
			throw new Exception('add_attribute: error - ' . $attribute_id->get_error_message());
		}

		return $attribute_id;
	}

	/**
	 * Обновление связи свойств классификатора 1С с общими атрибутами WooCommerce
	 *
	 * @param $relationship_attributes_by_classifier_properties
	 *
	 * @return bool
	 */
	public function update_relationship_attributes_by_classifier_properties($relationship_attributes_by_classifier_properties)
	{
		return update_option($this->get_prefix() . '_relationship_atts_cl_pr', $relationship_attributes_by_classifier_properties, 'no');
	}

	/**
	 * Добавление значения для общего атрибута
	 *
	 * @param $name
	 * @param $taxonomy_name
	 *
	 * @return mixed
	 * @throws Exception
	 */
	private function add_attribute_value($name, $taxonomy_name)
	{
		register_taxonomy($taxonomy_name, 'product');

		$value_result = wp_insert_term($name, $taxonomy_name, array
		(
			'description' => '',
			'parent' => 0,
			'slug' => '',
		));

		if(is_wp_error($value_result))
		{
			if(isset($value_result->error_data['term_exists']) && $value_result->error_data['term_exists'])
			{
				return $value_result->error_data['term_exists'];
			}

			throw new Exception('add_attribute_value: error - ' . $value_result->get_error_message());
		}

		if(isset($value_result['term_id']))
		{
			return $value_result['term_id'];
		}

		throw new Exception('add_attribute_value: error');
	}

	/**
	 * Получение названия таксономии атрибута по описанию
	 *
	 * @param $label
	 *
	 * @return string
	 */
	private function get_attribute_name_by_label($label)
	{
		$taxonomies = wp_list_pluck(wc_get_attribute_taxonomies(), 'attribute_name', 'attribute_label');

		return isset($taxonomies[$label]) ? $taxonomies[$label] : '';
	}

	/**
	 * Обновление свойств каталога
	 *
	 * @param $properties_data array - данные свойств из классификатора
	 *
	 * @return boolean|array
	 */
	public function update_classifier_properties($properties_data)
	{
		update_option($this->get_prefix() . '_classifier_properties', $properties_data, 'no');

		return $this->load_classifier_properties();
	}

	/**
	 * Получение массива кешированных свойств классификатора
	 *
	 * @return array
	 */
	public function load_classifier_properties()
	{
		return get_option($this->get_prefix() . '_classifier_properties', []);
	}

	/**
	 * Extract ZIP files
	 *
	 * @param $zip_file_path
	 *
	 * @return boolean|int
	 */
	private function extract_zip($zip_file_path)
	{
		$zip_archive = zip_open($zip_file_path);

		$img_files = 0;
		$error_files = 0;

		if(is_resource($zip_archive))
		{
			$this->logger()->info('extract_zip: unpack start - ' . $zip_file_path);

			while($zip_entry = zip_read($zip_archive))
			{
				$name = zip_entry_name($zip_entry);

				$this->logger()->info('extract_zip: unpack file name - ' . $name);

				$import_files = $this->file_type_detect($name);

				if($import_files == 'import_files')
				{
					$result = $this->extract_zip_image($zip_archive, $zip_entry, substr($name, $import_files));

					if($result == false)
					{
						$error_files++;
					}

					$img_files++;
				}
				else
				{
					$result = $this->extract_zip_xml($zip_archive, $zip_entry, $name);

					if($result == false)
					{
						$error_files++;
					}
				}
			}

			$this->logger()->info('extract_zip: unpack end - ' . $zip_file_path);

			zip_close($zip_archive);
		}
		else
		{
			$this->logger()->error('extract_zip: Zip_open error - ' . $zip_file_path);
			return false;
		}

		if($img_files > 0)
		{
			$this->logger()->info('extract_zip: unpack images count - ' . $img_files);
		}
		
		if($error_files > 0)
		{
			$this->logger()->error('extract_zip: unpack error files - ' . $img_files);
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
	private function extract_zip_xml($zip_arc, $zip_entry, $name)
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
	private function extract_zip_image($zipArc, $zip_entry, $name)
	{
		/**
		 * Extract to dir
		 */
		$import_files_dir = $this->get_upload_directory() . '/catalog/import_files/';

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