<?php

class Main extends Base
{
	private $batch;

	private $batchId;
	private $workerId;
	private $lastStepId;
	private $refreshStep = false;

	public function __construct()
	{
		parent::__construct();
		$this->store = new DataStore();

		$this->parseURL();

		// compile the batch script
		try 
		{
			$myBatchCompiler = new BatchCompiler($this->batchId);
			$this->batch = $myBatchCompiler->getBatch();
		} catch (Exception $e)
		{
			header("HTTP/1.0 500 Internal Server Error");
			echo $e->getMessage();
			die('Batch parsing failed or batch not found');
		}

		// read last step id
		$this->lastStepId = $this->store->read('stepId', -1);

		if ($this->lastStepId == -1)
		{
			$this->batch->init();
		}

		// process submitted post data
		$this->handlePostData();
	}

	public function render()
	{
		$tpl = new Template('main');

		$tpl->set('batchId', $this->batchId);
		$tpl->set('workerId', $this->workerId);
		$tpl->set('stepCount', $this->batch->countSteps());

		// render step or display error message
		try 
		{
			$stepId = $this->lastStepId + 1;

			if ($this->refreshStep) $stepId--;

			if (is_array($this->registry->get('errors')))
			{
				$tpl->set('msg', $this->registry->get('errors'));
			}

			if ($stepId < 0) $stepId = 0;
			if ($stepId >= $this->batch->countSteps()) $stepId = $this->batch->countSteps() - 1;

			$tpl->set('stepId', $stepId);
			$this->store->write('stepId', $stepId);

			$content = $this->batch->renderStep($stepId);
	
		} catch (Exception $e)
		{
			// display error message
			$errorTpl = new Template('error');
			$errorTpl->set('message', $e->getMessage());
			//$errorTpl->set('trace', $e->getTraceAsString());
			$tpl->set('nextStepId', -1);
			$content = $errorTpl->render();
		}

		$tpl->set('content', $content);

		// render main template
		$o = $tpl->render();

		return $o;
	}

	private function handlePostData()
	{
		if (!isset($_POST['stepId']))
		{
			if ($this->lastStepId < 0) return;
			// user hit "reload" in his browser, changed the browser, ...
			$this->refreshStep = true;

		} else {
			if (!is_numeric($_POST['stepId']))
			{
				$msg = array('invalid form data submitted');
				$this->refreshStep = true;
			}

			$stepId = $_POST['stepId'];
			settype($stepId, 'int');

			$data = $_POST;
			unset($data['batchId']);
			unset($data['stepId']);

			if ($stepId <> $this->lastStepId) {
				// user hit "reload" in his browser and sent the post data again
				$this->refreshStep = true;
			} else {
				$msg = $this->batch->validateAndSave($stepId, $data);
				if (is_array($msg)) $this->refreshStep = true;
			}
		}
		
		$this->registry->set('errors', $msg);
	}

	private function parseURL() 
	{
		$path = $_GET['path'];
		$path = explode('/', $path);

		if (count($path) < 2)
		{
			die('invalid URL');
		}

		// we only need the first two path elements
		while(count($path) > 2) array_pop($path);

		// extract batch id
		$this->batchId = preg_replace("/[^a-zA-Z0-9\s]/", "", $path[0]);
		$this->registry->set('batchId', $this->batchId);

		// extract worker id
		$this->workerId = preg_replace("/[^a-zA-Z0-9\s]/", "", $path[1]);
		$this->registry->set('workerId', $this->workerId);

		// handle manual restart
		if (isset($_GET['restart']))
		{
			$this->store->delete();
		}

		// read 'return' parameter
		if (isset($_GET['return']))
		{
			$returnBatch = $_GET['return'];
			$returnBatch = preg_replace("/[^a-zA-Z0-9\s]/", "", $returnBatch);
			$this->registry->set('returnBatch', $returnBatch);
			$this->store->write('returnBatch', $returnBatch);
		}
	}
}
