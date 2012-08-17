<?php

class Main extends Base
{
	private $batch;
	private $tpl;

	private $batchId;
	private $workerId;
	private $lastStepId;
	private $refreshStep = false;

	public function __construct($batchId, $workerId, $restart = false, $returnBatch = null)
	{
		parent::__construct();
		$this->store = new DataStore();

		$this->tpl = new Template('main');

		$this->batchId = $batchId;
		$this->registry->set('batchId', $batchId);
		$this->tpl->set('batchId', $this->batchId);

		$this->workerId = $workerId;
		$this->registry->set('workerId', $workerId);
		$this->tpl->set('workerId', $this->workerId);

		// handle manual restart
		if ($restart) $this->store->deleteWorker();

		// handle 'returnBatch' parameter
		if ($returnBatch <> null) {
			$this->registry->set('returnBatch', $returnBatch);
			$this->store->writeWorker('returnBatch', $returnBatch);
		}
	}

	public function render()
	{
		// compile the batch script
		$myBatchCompiler = new BatchCompiler($this->batchId);
		$this->batch = $myBatchCompiler->getBatch();

		// read last step id
		$this->lastStepId = $this->store->readWorker('stepId', -1);
		if ($this->lastStepId == -1) $this->batch->init();

		// process submitted post data
		$this->handlePostData();

		// calculate current step id
		$stepId = $this->lastStepId + 1;
		if ($this->refreshStep) $stepId--;
		if ($stepId < 0) $stepId = 0;
		if ($stepId >= $this->batch->countSteps()) $stepId = $this->batch->countSteps() - 1;
		$this->store->writeWorker('stepId', $stepId);

		// display error messages
		if (is_array($this->registry->get('errors'))) {
			$this->tpl->set('msg', $this->registry->get('errors'));
		}

		// set variables
		$this->tpl->set('stepId', $stepId);
		$this->tpl->set('stepCount', $this->batch->countSteps());
		$this->tpl->set('state', $this->batch->state());

		// render step
		$content = $this->batch->renderStep($stepId);
		$this->tpl->set('content', $content);

		return $this->tpl->render();
	}

	private function handlePostData()
	{
		$msg = '';
		
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
}
