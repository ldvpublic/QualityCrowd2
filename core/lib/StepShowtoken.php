<?php

class StepShowtoken extends Step
{
	protected function init() {}
	
	protected function prepareRender()
	{
		$token = $this->store->read('token');
		$this->tpl->set('token', $token);
	}

	public function validate(&$data) 
	{
		return true;
	}

	private function generateToken() {
		return uniqid();
	}
}
