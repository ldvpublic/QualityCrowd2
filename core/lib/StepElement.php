<?php

abstract class StepElement extends Base
{
	protected $command;
	protected $properties;
	protected $arguments;
	protected $tpl;
	
	protected $batch;
	protected $uid;

	abstract protected function init();
	abstract protected function prepareRender();
	abstract public function validate(&$data);

	public function __construct($elementArray, Batch $batch, $uid)
	{
		parent::__construct();

		$this->arguments = $elementArray['arguments'];
		$this->properties = $elementArray['properties'];
		$this->command = $elementArray['command'];

		$this->batch = $batch;
		$this->uid = $uid;
		
		$this->tpl = new Template('element.' . $elementArray['command'], $this->batch->id());

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
