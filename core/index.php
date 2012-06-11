<?php
require_once('lib/bootstrap.php');

//header('Content-Type: text/plain');

$myPage = new Main();
echo $myPage->render();
