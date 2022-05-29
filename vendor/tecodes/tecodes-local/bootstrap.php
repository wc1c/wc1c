<?php
/**
 * Bootstrap
 *
 * @package Tecodes/Local
 */
require_once __DIR__ . '/http/class-tecodes-local-http-basic-auth.php';
require_once __DIR__ . '/http/class-tecodes-local-http-exception.php';
require_once __DIR__ . '/http/class-tecodes-local-http-oauth.php';
require_once __DIR__ . '/http/class-tecodes-local-http-options.php';
require_once __DIR__ . '/http/class-tecodes-local-http-request.php';
require_once __DIR__ . '/http/class-tecodes-local-http-response.php';

require_once __DIR__ . '/interface/interface-tecodes-local.php';
require_once __DIR__ . '/interface/interface-tecodes-local-storage-code.php';
require_once __DIR__ . '/interface/interface-tecodes-local-code.php';
require_once __DIR__ . '/interface/interface-tecodes-local-http.php';
require_once __DIR__ . '/interface/interface-tecodes-local-instance.php';

require_once __DIR__ . '/storage/class-tecodes-local-storage-code.php';
require_once __DIR__ . '/class-tecodes-local-http.php';
require_once __DIR__ . '/class-tecodes-local-instance.php';
require_once __DIR__ . '/class-tecodes-local.php';