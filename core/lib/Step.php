<?php

class Step extends Base
{
	private $tpl;
	
	private $batch;
	private $workerId;
	private $stepId;
	private $elements;
	private $properties;

	public function __construct($stepArray, Batch $batch, $workerId, $stepId)
	{
		parent::__construct();

		$this->batch = $batch;
		$this->workerId = $workerId;
		$this->stepId = $stepId;
		$this->elements = $stepArray['elements'];
		$this->properties = $stepArray['properties'];
		
		$this->tpl = new Template('step', $this->batch->id());
	}

	// return true if this step should be skipped
	public function skip()
	{
		return false;
	}

	public function render()
	{
		if (is_array($this->properties)) {
			$this->tpl->setArray($this->properties);
		}

		$elementRenderings = array();
		
		foreach($this->elements as $ek => $element) 
		{
			$uid = hash("crc32b", $this->batch->id() . '-' . $this->stepId . '-' . $ek);

			$class = 'Element' . ucfirst($element['command']);
			$elementObject = new $class($element, $this->batch, $uid);

			$elementRenderings[] = $elementObject->render();
		}

		$this->tpl->set('elements', $elementRenderings);
		return $this->tpl->render();
	}

	public function validate(&$data) 
	{
		if ($this->properties['skipvalidation']) return true;

		$msgs = array();

		foreach($this->elements as $ek => $element) 
		{
			$uid = hash("crc32b", $this->batch->id() . '-' . $this->stepId . '-' . $ek);

			$class = 'Element' . ucfirst($element['command']);
			$elementObject = new $class($element, $this->batch, $uid);

			$msg = $elementObject->validate($data);

			if (is_array($msg)) {
				$msgs = array_merge($msgs, $msg);
			}
		}

		if (count($msgs) > 0) return $msgs;
		return true;
	} 

	public function save($data)
	{
		$data = array('timestamp' => time()) + $data;
		$data = array('stepId' => $this->stepId) + $data;

		$this->store->writeWorkerCSV('results', array($data), $this->batch->id(), $this->workerId);
	}
}
