<?php

abstract class Step extends Base
{
	protected $command;
	protected $properties;
	protected $arguments;
	protected $tpl;

	protected $autoRender = true;
	
	abstract protected function init();
	abstract protected function prepareRender();
	abstract public function validate(&$data);

	public function __construct($stepArray)
	{
		parent::__construct();

		$this->arguments = $stepArray['arguments'];
		$this->properties = $stepArray['properties'];
		$this->command = $stepArray['command'];
		
		if ($this->autoRender)
		{
			$this->tpl = new Template($stepArray['command']);
		}

		$this->init();
	}

	public function render()
	{
		if (!$this->autoRender) return;
		
		if (is_array($this->properties))
		{
			$this->tpl->setArray($this->properties);
		}
		
		$this->prepareRender();

		return $this->tpl->render();
	}
}
