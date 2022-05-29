<?php
/**
 * Tecodes local interface
 *
 * @package Tecodes/Local
 */
interface Interface_Tecodes_Local
{
	/**
	 * Установка клиента для HTTP запросов
	 *
	 * @param Interface_Tecodes_Local_Http $http
	 *
	 * @return bool
	 */
	public function set_http($http);

	/**
	 * Установка сервера API
	 *
	 * @param $server
	 *
	 * @return bool
	 */
	public function api_set_server($server);

	/**
	 * POST method
	 *
	 * @param string $endpoint API endpoint
	 * @param array $data Request data
	 *
	 * @return array
	 */
	public function api_post($endpoint, $data);

	/**
	 * PUT method
	 *
	 * @param string $endpoint API endpoint
	 * @param array $data Request data
	 *
	 * @return array
	 */
	public function api_put($endpoint, $data);

	/**
	 * GET method
	 *
	 * @param string $endpoint API endpoint
	 * @param array $parameters Request parameters
	 *
	 * @return array
	 */
	public function api_get($endpoint, $parameters = []);

	/**
	 * DELETE method
	 *
	 * @param string $endpoint API endpoint
	 * @param array $parameters Request parameters
	 *
	 * @return array
	 */
	public function api_delete($endpoint, $parameters = []);

	/**
	 * OPTIONS method
	 *
	 * @param string $endpoint API endpoint
	 *
	 * @return array
	 */
	public function api_options($endpoint);

	/**
	 * Получение статуса сервера
	 * Используется для направления запросов только на активный в данный момент сервер.
	 *
	 * @return string inactive or active
	 */
	public function api_get_status();

	/**
	 * Получение данных кода с сервера
	 *
	 * Данные получаются с активного сервера. Если сервер выключен, данные не будут получены.
	 * Если не передать код в качестве аргумента, он будет взят из ранее установленного в окружении.
	 *
	 * @param string $code
	 *
	 * @return bool|array
	 */
	public function api_get_code_data_by_name($code = '');

	/**
	 * Получение данных локального кода с сервера
	 *
	 * @return bool|string
	 */
	public function api_get_local_code();

	/**
	 * Установка кода
	 *
	 * @param $code
	 *
	 * @return bool
	 */
	public function set_code($code);

	/**
	 * Получение текущего кода
	 *
	 * @return bool|string
	 */
	public function get_code();

	/**
	 * Установка хранилища локального кода
	 *
	 * @param Interface_Tecodes_Local_Storage_Code $storage
	 *
	 * @return bool
	 */
	public function set_local_code_storage($storage);

	/**
	 * Установка данных локального кода
	 *
	 * @param $code
	 *
	 * @return bool
	 */
	public function set_local_code($code);

	/**
	 * Получение данных локального кода
	 *
	 * @return bool|string
	 */
	public function get_local_code();

	/**
	 * Валидация
	 *
	 * @return bool
	 */
	public function validate();

	/**
	 * Маркер сосотояния текущей валидации
	 *
	 * @return bool
	 */
	public function is_valid();

	/**
	 * Сохранение локального кода
	 *
	 * @param string $local_code
	 *
	 * @return bool
	 */
	public function update_local_code($local_code);

	/**
	 * Удаление локального кода
	 *
	 * @return bool
	 */
	public function delete_local_code();

	/**
	 * Получение ошибок
	 *
	 * В случае возникновения каких либо ошибок, возвращается ассоциативный массив (ключ - значение):
	 * - ключ: код ошибки
	 * - значение: расшифровка ошибки на доступном языке. По умолчанию Английский.
	 *
	 * @return false|array
	 */
	public function get_errors();
}