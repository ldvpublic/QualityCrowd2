<?php

class StepPage extends Step
{
	protected function init() {}
	
	public function validate(&$data) 
	{
		return true;
	}

	protected function prepareRender()
	{
		$this->tpl->set('text', $this->properties['text']);
	}
}
