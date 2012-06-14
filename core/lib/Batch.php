<?php

class Batch extends Base
{
	private $batchId;
	private $steps;
	private $meta;

	public function __construct($batchId, $meta, $steps) 
	{
		parent::__construct();

		$this->batchId = $batchId;
		$this->meta = $meta;
		$this->steps = $steps;
	}

	public function __sleep() 
	{
		parent::__sleep();
		return array('batchId', 'steps', 'meta');
	}

	public function __wakeup() 
	{
		parent::__wakeup();
	}

	public function init()
	{
		$this->generateToken();
		$this->collectMetaData();
	}

	public function validateAndSave($stepId, $data)
	{
		// validate data
		$stepObject = $this->getStepObject($stepId);
		$msg = $stepObject->validate(&$data);

		// save result data to csv file
		if ($msg === true) {
			$data = array('timestamp' => time()) + $data;
			$data = array('command' => $this->steps[$stepId]['command']) + $data;
			$data = array('stepId' => $stepId) + $data;
			$store = new DataStore();
			$store->writeCSV('results', array($data));
		} 

		return $msg;
	}

	public function renderStep($stepId)
	{
		if ($stepId < 0 || $stepId >= count($this->steps))
		{
			throw new Exception('Invalid step id');
		}

		$stepObject = $this->getStepObject($stepId);
		return $stepObject->render();
	}

	public function countSteps()
	{
		return count($this->steps);
	}

	public function meta($key = null)
	{
		if ($key == null)
		{
			return $this->meta;
		} else
		{
			return $this->meta[$key];
		}
	}

	public function steps()
	{
		return $this->steps;
	}

	public function workers()
	{
		$workers = array();
		$path = DATA_PATH . $this->batchId . '/';
		$files = glob($path . '*', GLOB_MARK);
	    foreach ($files as $file) 
	    {
	    	$file = preg_replace('#^' . $path . '#', '', $file);
	    	$file = preg_replace('#/$#', '', $file);
	    	$workers[$file] = $file;
	    }

	    return $workers;
	}

	private function getStepObject($stepId)
	{
		$step = $this->steps[$stepId];

		if (!isset($step['command']))
		{
			throw new Exception('internal error');
		}

		switch($step['command'])
		{
			case 'video':
			case 'image':
			case 'question':
			$stepObject = new StepQuestion($step);
			break;

			default:
			$class = 'Step' . ucfirst($step['command']);
			$stepObject = new $class($step);			
		}

		return $stepObject;
	}

	private function collectMetaData()
	{
		$meta = array(
			array('workerId', $this->registry->get('workerId')),
			array('token', $this->registry->get('token')),
			array('timestamp', time()),
			array('remoteaddr', md5($_SERVER['REMOTE_ADDR'])),
			array('useragent', $_SERVER['HTTP_USER_AGENT']),
		);

		$this->store->writeCSV('meta', $meta);
	}

	private function generateToken() 
	{
		$token = $this->getConfig('securitySalt') . '-'; 
		$token .= $this->registry->get('batchId') . '-';
		$token .= $this->registry->get('workerId') . '-';
		$token .= md5($_SERVER['HTTP_USER_AGENT']) . '-';
		$token .= md5($_SERVER['REMOTE_ADDR']) . '-';
		$token .= date('d.m.Y');

		$token = md5($token);
		$token = substr($token, 20);

		$this->registry->set('token', $token);
		$this->store->write('token', $token);
	}
}