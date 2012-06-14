<?php

class Request extends Base
{
	public function __construct()
	{
		parent::__construct();
	}

	public function process()
	{
		$path = $_GET['path'];
		$path = explode('/', $path);
		$path = array_filter($path); // remove empty elements

		if (count($path) < 1) {
			header('Location: ' . BASE_URL . 'admin/batches');
			exit;
		}

		if ($path[0] == 'admin')
		{
			$username = $this->login();

			if (count($path) < 2) {
				header('Location: ' . BASE_URL . 'admin/batches');
				exit;
			}

			$admin = new Admin($username, $path[1]);
			echo $admin->render();
			return;
		} else
		{
			$this->processMain($path);
			return;
		}
	}

	private function processMain($path)
	{
		if (count($path) < 2) die('invalid URL');

		// extract batch id
		$batchId = preg_replace("/[^a-zA-Z0-9\s-]/", "", $path[0]);

		// extract worker id
		$workerId = preg_replace("/[^a-zA-Z0-9\s-]/", "", $path[1]);

		$returnBatch = null;
		if (isset($_GET['return']))
		{
			$returnBatch = $_GET['return'];
			$returnBatch = preg_replace("/[^a-zA-Z0-9\s]/", "", $returnBatch);
		}

		// handle manual restart
		$restart = false;
		if (isset($_GET['restart']))
		{
			$restart = true;
		}

		$myPage = new Main($batchId, $workerId, $restart, $returnBatch);
		echo $myPage->render();
	}

	private function login()
	{
		if (isset($_SERVER['PHP_AUTH_USER'])) {
		    $username = $_SERVER['PHP_AUTH_USER'];
		    $password = $_SERVER['PHP_AUTH_PW'];

		    $username = $this->auth($username, $password);
		    if ($username !== false)
		    {
		    	return $username;
		    } else 
		    {
		    	sleep(1);
		    }
		}

		header('WWW-Authenticate: Basic realm="QualityCrowd"');
	    header('HTTP/1.0 401 Unauthorized');
	    echo 'Unauthorized';
	    exit;
	}

	private function auth($username, $password)
	{
		$salt = $this->getConfig('securitySalt');
		$users = $this->getConfig('adminUsers');
		$hash = $users[$username];

		if ($hash == sha1($password . $salt))
		{
			return $username;
		} else
		{
			return false;
		}
	}
}
