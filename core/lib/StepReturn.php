<?php

class StepReturn extends Step
{
	protected $autoRender = false;

	protected function init() 
	{
		$returnBatch = $this->store->readWorker('returnBatch', false);
		if ($returnBatch === false)
		{
			throw new Exception("No return target found");
		}

		$this->store->deleteWorker('returnBatch');
		$this->store->writeWorker('done', true);

		$url = BASE_URL . $returnBatch;
		$url .= '/' . $this->registry->get('workerId');
		header("Location: $url");
		exit;
	}

	public function validate(&$data) 
	{
		return true;
	}

	protected function prepareRender()
	{

	}
}
