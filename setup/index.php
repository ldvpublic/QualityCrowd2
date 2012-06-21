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
 * create directories
 */

$dirs = array(
	$rootPath . 'batches',
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
			$err = "'$parent' is not writable, run <code>chmod o+w $parent</code>";
			goto webpage;
		}

		mkdir($dir, 0777);
		@chmod($dir, 0777);
		$msg = array("created $dir");
	}

	if (!is_writable($dir))
	{
		$err = "'$dir' is not writable, run <code>chmod o+w $dir</code>";
		goto webpage;
	}
}

/*
 * setup other .htaccess files
 */
if (!file_exists($rootPath . 'data/.htaccess'))
{
	file_put_contents($rootPath . 'data/.htaccess', "Deny from all\n");
	$msg = array("written {$rootPath}data/.htaccess");
}

if (!file_exists($rootPath . 'batches/.htaccess'))
{
	file_put_contents($rootPath . 'batches/.htaccess', "Deny from all\n");
	$msg = array("written {$rootPath}batches/.htaccess");
}

/*
 * install example scripts
 */
copy_r($rootPath . 'setup/example-batches', $rootPath . 'batches/');
$msg = array("installed example batches");

/*
 * check permissions for core
 */
if (is_writable($rootPath . 'core'))
{
	$err = "'{$rootPath}core' is writable";
	goto webpage;
}


/*
 * setup main .htaccess file
 */

$htaccess = file_get_contents($rootPath . 'setup/main.htaccess');
$htaccess = str_replace('##BASEURL##', $baseURL, $htaccess);
file_put_contents($rootPath . '.htaccess', $htaccess);
$msg = array("written $rootPath.htaccess");

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
