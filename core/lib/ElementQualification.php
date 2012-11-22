<?php

class ElementQualification extends StepElement
{
	private $qualiMain;

	protected function init() 
	{
		$qualiBatch = $this->arguments[0];

		if (!$this->skip()) {
			$this->qualiMain = new Main($qualiBatch, $this->workerId, 'qualification-main');
		}
	}

	public function skip()
	{
		$qualiBatch = $this->arguments[0];
		$done = $this->store->readWorker('done', false, $qualiBatch, $this->workerId);
		return $done;
	}

	public function validate(&$data) 
	{
		if (!isset($this->qualiMain)) return true;

		$qualiStepId = $data['stepId-qualification-main'];

		if ($this->qualiMain->getBatch()->countSteps() == $qualiStepId + 1) {
			return true;
		} else {
			return false;
		}
	}

	protected function prepareRender()
	{
		if (isset($this->qualiMain)) {
			$this->tpl->set('content', $this->qualiMain->render());
		} else {
			$this->tpl->set('content', 'NOTHING TO DO');
		}
	}
}
