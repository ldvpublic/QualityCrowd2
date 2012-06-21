<?php

die('Forbidden');

require('fstools.php');

$rootPath = preg_replace('#setup/uninstall.php$#', '', __FILE__);

rrmdir($rootPath . 'batches');
rrmdir($rootPath . 'data');
rrmdir($rootPath . 'media');
rrmdir($rootPath . 'core/tmp');

echo "Done";
