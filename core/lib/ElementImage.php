<?php

class ElementImage extends StepElement
{
	protected function init() 
	{
		
	}

	public function validate(&$data) 
	{
		return true;
	}

	protected function prepareRender()
	{
		$this->tpl->set('image', $this->properties['mediaurl'] . $this->arguments['image']);
	}
}
