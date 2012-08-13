<?php
// ONLY FOR DEVELOPMENT!

// to use the uninstaller uncomment the following line
// CAUTION: this uninstaller deletes all batches and results
die('Forbidden');

require('fstools.php');

$rootPath = preg_replace('#setup' . DSX . 'uninstall.php$#', '', __FILE__);

rrmdir($rootPath . 'batches');
rrmdir($rootPath . 'data');
rrmdir($rootPath . 'media');
rrmdir($rootPath . 'core' . DS . 'tmp');
unlink($rootPath . '.htaccess');

echo "Done";
