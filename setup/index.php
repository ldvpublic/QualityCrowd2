<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('display_errors', 'On');

// handle the funny windows backslash
define('DS', DIRECTORY_SEPARATOR);
define('DSX', preg_quote(DS));

$fileMode = 0666;
$dirMode = 0777;

// initialize variables for the error messages through the setup process
$err = '';
$msg = array();

/*
 * Determine paths and URLs
 */
$rootPath = preg_replace('#setup' . DSX . 'index.php$#', '', __FILE__);
$baseURL = preg_replace('#setup/index.php$#', '', $_SERVER['PHP_SELF']);

require($rootPath.'core'.DS.'lib'.DS.'fstools.php');

/*
 * check if setup is disabled
 */
if (file_exists($rootPath.DS.'setup'.DS.'disabled.php'))
{
	$err = "setup disabled";
	goto webpage;
}

/*
 * check PHP config
 */
if (ini_get('short_open_tag') == '' && version_compare(PHP_VERSION, '5.4.0', '<'))
{
	$err = "enable 'short_open_tag' in your PHP configuration or update to PHP 5.4.0 or later";
	goto webpage;
}
$msg[] = "checked PHP configuration";

/*
 * check support for mod_rewrite
 */

if (function_exists('apache_get_modules')) {
	$modules = apache_get_modules();
	$mod_rewrite = in_array('mod_rewrite', $modules);
	if (!$mod_rewrite)
	{
		$err = "mod_rewrite not available";
		goto webpage;
	}
	$msg[] = "checked mod_rewrite support";
}



/* 
 * create directories
 */
$dirs = array(
	$rootPath . 'data',
	$rootPath . 'media',
	$rootPath . 'core' . DS . 'tmp',
	$rootPath . 'core' . DS . 'tmp' . DS . 'batch-cache',
	$rootPath . 'core' . DS . 'tmp' . DS . 'img-cache',
	$rootPath . 'core' . DS . 'tmp' . DS . 'browscap',
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

		mkdir($dir, $dirMode);
		$msg[] = "created $dir";
	}

	if (!is_writable($dir))
	{
		$err = "'$dir' is not writable";
		goto webpage;
	}
}

/*
 * install example scripts
 */
if (!file_exists($rootPath . 'batches'))
{
	rcopy($rootPath . 'setup' . DS . 'example-batches', $rootPath . 'batches');
	$msg[] = "installed example batches";
}

/*
 * setup main .htaccess file
 */

if (!file_exists($rootPath . '.htaccess'))
{
	$htaccess = file_get_contents($rootPath.'setup'.DS.'main.htaccess');
	$htaccess = str_replace('##BASEURL##', $baseURL, $htaccess);
	file_put_contents($rootPath . '.htaccess', $htaccess);
	$msg[] = "written $rootPath.htaccess";
}

/*
 * setup other .htaccess files
 */
if (!file_exists($rootPath.'data'.DS.'.htaccess'))
{
	file_put_contents($rootPath.'data'.DS.'.htaccess', "Deny from all\n");
	$msg[] = "written {$rootPath}data".DS.".htaccess";
}

if (!file_exists($rootPath.'batches'.DS.'.htaccess'))
{
	file_put_contents($rootPath.'batches'.DS.'.htaccess', "Deny from all\n");
	$msg[] = "written {$rootPath}batches".DS.'.htaccess';
}
if (!file_exists($rootPath.'core'.DS.'tmp'.DS.'.htaccess'))
{
	file_put_contents($rootPath.'core'.DS.'tmp'.DS.'.htaccess', "Deny from all\n");
	$msg[] = "written {$rootPath}core".DS.'tmp'.DS.'.htaccess';
}
if (!file_exists($rootPath.'core'.DS.'tmp'.DS.'img-cache'.DS.'.htaccess'))
{
	file_put_contents($rootPath.'core'.DS.'tmp'.DS.'img-cache'.DS.'.htaccess', "Allow from all\n");
	$msg[] = "written {$rootPath}core".DS.'tmp'.DS.'img-cache'.DS.'.htaccess';
}

/* 
 * fix permissions
 */
$dirs = array(
	$rootPath . 'batches',
	$rootPath . 'data',
	$rootPath . 'media',
	$rootPath . 'core' . DS . 'tmp',
);
foreach($dirs as $dir)
{
	if (rchmod($dir, $fileMode, $dirMode)) {
		$msg[] = "fixed permssions for $dir";
	} else {
		$msg[] = "error fixing permssions for $dir";
	}
}

/*
 * disable setup script
 */
$dir = $rootPath.'setup'.DS;
if (!is_writable($dir))
{
	$err = "'$dir' is not writable";
	goto webpage;
}
if (file_put_contents($dir.'disabled.php', "<?php
// to reenable the setup script delete this file"))
{
	chmod($dir.'disabled.php', $fileMode);
	$msg[] = "disabled setup script";
} else {

}

/*
 * display webpage
 */

webpage:

$returnPage = (isset($_GET['r']) ? '/' . $_GET['r'] : '');

?>
<!doctype html>
<html>
	<head>
		<title>QualityCrowd - Setup</title>

		<link rel="stylesheet" href="<?= $baseURL ?>core/files/css/style.css" />
	</head>
	<body>
		<div class="header">
			<h1>QualityCrowd</h1>
		</div>
		
		<h2>Setup</h2>

		<?php if (count($msg)):?>
		<h3>Done</h3>
		<?php endif; ?>
		<ul>
			<?php foreach($msg as $m): ?>
			<li><?= $m ?></li>
			<?php endforeach; ?>
		</ul>

		<?php if ($err == ''): ?>
		<p>Setup complete</p>
		<p>
			<a href="<?= $baseURL?>admin<?= $returnPage ?>">
				<?= ($returnPath = '' ? 'Admin Panel' : 'Return') ?>
			</a>
		</p>

		<?php else: ?>
		<h3>Error</h3>
		<ul class="errormessage">
			<li><?= $err ?></li>
		</ul>
		<p><a href="<?= $baseURL?>setup/index.php">Retry</a></p>
		<?php endif; ?>
		<div class="footer">
		</div>
	</body>
</html>
