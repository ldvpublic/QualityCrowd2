<?php

abstract class StepElement extends Base
{
	protected $command;
	protected $properties;
	protected $arguments;
	protected $tpl;
	
	protected $step;
	protected $uid;

	abstract protected function init();
	abstract protected function prepareRender();
	abstract public function validate(&$data);

	public function __construct($elementArray, Step $step, $uid)
	{
		parent::__construct();

		$this->arguments = $elementArray['arguments'];
		$this->properties = $elementArray['properties'];
		$this->command = $elementArray['command'];

		$this->step = $step;
		$this->uid = $uid;
		
		$this->tpl = new Template('element.' . $elementArray['command'], $this->step->batch()->id());

		$this->init();
	}

	// return true if this step should be skipped
	// this is only a default implementation, gets overloaded in child classes
	public function skip()
	{
		return false;
	}

	public function render()
	{
		if (is_array($this->properties)) {
			$this->tpl->setArray($this->properties);
		}
		if (is_array($this->arguments)) {
			$this->tpl->setArray($this->arguments);
		}

		$this->tpl->set('uid', $this->uid);
		
		$this->prepareRender();

		return $this->tpl->render();
	}
}
