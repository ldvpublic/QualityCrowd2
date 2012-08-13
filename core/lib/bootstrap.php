<?php
// handle the funny windows backslash
//   DS is the directory separator
//   DSX is the escaped DS for the use in a PCRE
define('DS', DIRECTORY_SEPARATOR);
define('DSX', preg_quote(DS));

// calculate root path
$rootPath = preg_replace('#core' . DSX . 'lib' . DSX . 'bootstrap.php$#', '', __FILE__);

// read config file
$cf = require($rootPath . 'core' . DS . 'config.php');

define('ROOT_PATH', $rootPath);
define('BATCH_PATH', $rootPath . 'batches' . DS);
define('DATA_PATH', $rootPath . 'data' . DS);
define('MEDIA_PATH', $rootPath . 'media' . DS);
define('LIB_PATH', $rootPath . 'core' . DS . 'lib' . DS);
define('TMP_PATH', $rootPath . 'core' . DS . 'tmp' . DS);
define('TEMPLATE_PATH', $rootPath . 'core' . DS . 'template' . DS);

$baseURL = 'http://' . $_SERVER['HTTP_HOST'];
$baseURL .= preg_replace('#core/index.php$#', '', $_SERVER['PHP_SELF']);

define('BASE_URL', $baseURL);
define('MEDIA_URL', $baseURL . 'media/');

spl_autoload_register(function ($class) 
{
	$file = LIB_PATH . $class . '.php';
	if (!file_exists($file))
	{
		throw new Exception("Class \"$class\" not found");
	}
	include_once($file);
});