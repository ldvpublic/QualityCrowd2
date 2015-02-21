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
		if ($path[count($path) - 1] == '') array_pop($path);

		if (count($path) < 1) {
			header('Location: ' . BASE_URL . 'admin');
			exit;
		}

		if ($path[0] == 'setup')
		{
			header('Location: ' . BASE_URL . 'admin');
			exit;
		}

		if ($path[0] == 'admin')
		{
			$username = $this->login();

			array_shift($path);
			if (count($path) == 0) $path[] = 'batches';
			$admin = new Admin($username, $path);
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
		$batchId = preg_replace("/[^a-zA-Z0-9-]/", "", $path[0]);
		if ($batchId == '') die('invalid URL');

		// extract worker id
		$workerId = $path[1];		

		// check worker id
		if(preg_match('/[^a-zA-Z0-9]/i', $path[1]) || 
		   $workerId == '' || 
		   strlen($workerId) > 64) 
		{
			die('invalid worker id');
		}

		// handle manual restart
		$restart = false;
		if (isset($_GET['restart']))
		{
			$restart = true;
		}

		$myPage = new Main($batchId, $workerId, 'main', $restart);

		try {
			echo $myPage->render();
		} catch (Exception $e) {
			return $this->renderException($e);
		}	
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

	private function renderException(Exception $e)
	{
		$errorTpl = new Template('error');
		$errorTpl->set('message', $e->getMessage());
		$errorTpl->set('trace', $e->getTraceAsString());

		echo $errorTpl->render();
	}
}
