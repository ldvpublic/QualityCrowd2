<?php

/*
 * Determine paths and URLs
 */
$rootPath = preg_replace('#setup/index.php$#', '', __FILE__);
$baseURL = preg_replace('#setup/index.php$#', '', $_SERVER['PHP_SELF']);

/* 
 * create directories
 */

$dirs = array(
	$rootPath . 'data',
	$rootPath . 'media',
	$rootPath . 'core/tmp',
	$rootPath . 'core/tmp/batch-cache',
);

foreach($dirs as $dir)
{
	mkdir($dir, 0777);
	chmod($dir, 0777);
}

/*
 * setup main .htaccess file
 */

$htaccess = file_get_contents($rootPath . 'setup/main.htaccess');
$htaccess = str_replace('##BASEURL##', $baseURL, $htaccess);
//file_put_contents($rootPath . '.htaccess', $htaccess);

/*
 * setup other .htaccess files
 */

file_put_contents($rootPath . 'data/.htaccess', "Deny from all\n");

echo "Done";