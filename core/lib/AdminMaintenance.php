<?php

class AdminMaintenance extends AdminPage
{
	protected function prepareRender()
	{
		$action = (isset($this->path[1]) ? $this->path[1] : '');
		$message = '';

		switch($action)
		{
			case '':
				break;

			case 'cleancache':
				$this->cleanCache();
				$message = 'All caches were cleaned.';
				break;

			case 'setup':
				$this->setup();
				break;

			default:
				$message = 'Not found';
				break;
		}

		$this->tpl->set('message', $message);

	}

	private function cleanCache()
	{
		$cachePaths = array(
			ROOT_PATH.'core'.DS.'tmp'.DS.'img-cache'.DS,
			ROOT_PATH.'core'.DS.'tmp'.DS.'batch-cache'.DS,
		);

		foreach($cachePaths as $path)
		{
			$files = glob($path . '*', GLOB_MARK);
		    foreach ($files as $file) 
		    {
		    	unlink($file);
		    }
		}

	}

	private function setup() 
	{
		@unlink(ROOT_PATH.'setup'.DS.'disabled.php');
		header('Location: ' . BASE_URL . 'setup/index.php?r=maintenance');
		exit;
	}
}
