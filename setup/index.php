<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors','On');

require('fstools.php');

$err = '';
$msg = array();

/*
 * Determine paths and URLs
 */
$rootPath = preg_replace('#setup/index.php$#', '', __FILE__);
$baseURL = preg_replace('#setup/index.php$#', '', $_SERVER['PHP_SELF']);

/*
 * check support for mod_rewrite
 */

if (function_exists('apache_get_modules')) {
	$modules = apache_get_modules();
	$mod_rewrite = in_array('mod_rewrite', $modules);
} else {
	$mod_rewrite =  getenv('HTTP_MOD_REWRITE') == 'On' ? true : false ;
}

if (!$mod_rewrite)
{
	$err = "mod_rewrite not available";
	goto webpage;
}
$msg[] = "checked mod_rewrite support";


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
	if (!file_exists($dir)) 
	{
		$parent = dirname($dir);
		if (!is_writable($parent))
		{
			$err = "'$parent' is not writable";
			goto webpage;
		}

		mkdir($dir, 0755);
		@chmod($dir, 0755);
		$msg[] = "created $dir";
	}

	if (!is_writable($dir))
	{
		$err = "'$dir' is not writable";
		goto webpage;
	}
}


/*
 * setup other .htaccess files
 */
if (!file_exists($rootPath . 'data/.htaccess'))
{
	file_put_contents($rootPath . 'data/.htaccess', "Deny from all\n");
	$msg[] = "written {$rootPath}data/.htaccess";
}

if (!file_exists($rootPath . 'batches/.htaccess'))
{
	file_put_contents($rootPath . 'batches/.htaccess', "Deny from all\n");
	$msg[] = "written {$rootPath}batches/.htaccess";
}


/*
 * install example scripts
 */
if (!file_exists($rootPath . 'batches'))
{
	rcopy($rootPath . 'setup/example-batches', $rootPath . 'batches');
	$msg[] = "installed example batches";
}

/*
 * setup main .htaccess file
 */

$htaccess = file_get_contents($rootPath . 'setup/main.htaccess');
$htaccess = str_replace('##BASEURL##', $baseURL, $htaccess);
file_put_contents($rootPath . '.htaccess', $htaccess);
$msg[] = "written $rootPath.htaccess";


/*
 * display webpage
 */

webpage:
?>
<!doctype html>
<html>
	<head>
		<title>QualityCrowd - Setup</title>

		<link rel="stylesheet" href="<?= $baseURL ?>core/files/css/style.css" />
	</head>
	<body>
		<div class="header">
			<h1>Setup</h1>
		</div>
		<h3>Done</h3>
		<ul>
			<?php foreach($msg as $m): ?>
			<li><?= $m ?></li>
			<?php endforeach; ?>
		</ul>

		<?php if ($err == ''): ?>
		<p>Setup complete</p>
		<p><a href="<?= $baseURL?>admin">Admin Panel</a></p>

		<?php else: ?>
		<h3>Error</h3>
		<ul class="errormessage">
			<li><?= $err ?></li>
		</ul>
		<p><a href="<?= $baseURL?>setup">Retry</a></p>
		<?php endif; ?>
		<div class="footer">
		</div>
	</body>
</html>
