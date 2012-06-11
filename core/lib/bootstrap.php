<?php

$rootPath = preg_replace('#core/lib/bootstrap.php$#', '', __FILE__);

define('ROOT_PATH', $rootPath);
define('BATCH_PATH', $rootPath . 'batches/');
define('DATA_PATH', $rootPath . 'data/');
define('MEDIA_PATH', $rootPath . 'media/');
define('LIB_PATH', $rootPath . 'core/lib/');
define('TMP_PATH', $rootPath . 'core/tmp/');
define('TEMPLATE_PATH', $rootPath . 'core/template/');

$baseURL = 'http://' . $_SERVER['HTTP_HOST'];
$baseURL .= preg_replace('#core/index.php$#', '', $_SERVER['PHP_SELF']);

define('BASE_URL', $baseURL);
define('MEDIA_URL', $baseURL . 'media/');

define('TOKEN_SALT', 'QualityCrowd2-7078e430');

spl_autoload_register(function ($class) 
{
	$file = LIB_PATH . $class . '.php';
	if (!file_exists($file))
	{
		throw new Exception("Class \"$class\" not found");
	}
	include_once($file);
});