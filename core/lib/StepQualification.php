<?php

class StepQualification extends Step
{
	//protected $autoRender = false;

	protected function init() 
	{
		$qualiBatch = $this->arguments[0];
		$done = $this->store->read('done', false, $qualiBatch);

		if (!$done)
		{
			// redirect to qualification batch
			$url = BASE_URL . $qualiBatch;
			$url .= '/' . $this->registry->get('workerId');
			$url .= '?return=' . $this->registry->get('batchId');
			header("Location: $url");
			exit;
		}
	}

	public function validate(&$data) 
	{
		return true;
	}

	protected function prepareRender()
	{

	}
}
