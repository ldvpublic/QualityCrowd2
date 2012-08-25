<?php

abstract class Step extends Base
{
	protected $command;
	protected $properties;
	protected $arguments;
	protected $tpl;
	
	protected $batchId;
	protected $workerId;
	protected $stepId;

	abstract protected function init();
	abstract protected function prepareRender();
	abstract public function validate(&$data);

	public function __construct($stepArray, $batchId, $workerId, $stepId)
	{
		parent::__construct();

		$this->arguments = $stepArray['arguments'];
		$this->properties = $stepArray['properties'];
		$this->command = $stepArray['command'];

		$this->batchId = $batchId;
		$this->workerId = $workerId;
		$this->stepId = $stepId;
		
		$this->tpl = new Template($stepArray['command'], $this->batchId);

		$this->init();
	}

	// return true if this step should be skipped
	public function skip()
	{
		return false;
	}

	public function render()
	{
		if (is_array($this->properties))
		{
			$this->tpl->setArray($this->properties);
		}
		
		$this->prepareRender();

		return $this->tpl->render();
	}

	public function save($data)
	{
		$data = array('timestamp' => time()) + $data;
		$data = array('command' => $this->command) + $data;
		$data = array('stepId' => $this->stepId) + $data;

		$this->store->writeWorkerCSV('results', array($data), $this->batchId, $this->workerId);
	}
}
