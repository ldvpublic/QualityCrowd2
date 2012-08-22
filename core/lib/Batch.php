<?php

class Batch extends Base
{
	private $batchId;
	private $steps;
	private $meta;

	private $state;

	public function __construct($batchId, $meta, $steps) 
	{
		parent::__construct();

		$this->batchId = $batchId;
		$this->meta = $meta;
		$this->steps = $steps;

		$this->state = $this->store->readBatch('state', 'edit', $this->batchId);
	}

	public function __sleep() 
	{
		parent::__sleep();
		return array('batchId', 'steps', 'meta');
	}

	public function __wakeup() 
	{
		parent::__wakeup();
		$this->state = $this->store->readBatch('state', 'edit', $this->batchId);
	}

	public static function readableState($state) 
	{
		switch($state)
		{
			case 'edit':   return 'Edit';
			case 'active': return 'Active';
			case 'post':   return 'Complete';
		}
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
		$msg = $stepObject->validate($data);

		// save result data to csv file
		if ($msg === true) {
			$data = array('timestamp' => time()) + $data;
			$data = array('command' => $this->steps[$stepId]['command']) + $data;
			$data = array('stepId' => $stepId) + $data;
			$store = new DataStore();
			$store->writeWorkerCSV('results', array($data));
		} 

		return $msg;
	}

	public function renderStep($stepId)
	{
		if ($stepId < 0 || $stepId >= count($this->steps)) {
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
		if ($key == null) {
			return $this->meta;
		} else {
			return $this->meta[$key];
		}
	}

	public function steps()
	{
		return $this->steps;
	}

	public function lockingUpdate($workerId)
	{
		// read table
		$lockingTable = $this->store->readBatch('locking', array(), $this->batchId);

		// clean table
		foreach($lockingTable as $wid => $value)
		{
			if ($value == 'finished') continue;
			if ($value < (time() - $this->meta['timeout'])) unset($lockingTable[$wid]);
		}

		// check new worker
		if (!array_key_exists($workerId, $lockingTable)) {
			if ($this->meta['workers'] > 0 && count($lockingTable) >= $this->meta['workers']) {
				return false;
			}
		} else {
			if ($lockingTable[$workerId] == 'finished') return true;
		}

		// set lock
		$lockingTable[$workerId] = time();
		
		// write table
		$this->store->writeBatch('locking', $lockingTable);
		return true;
	}

	public function lockingFinish($workerId)
	{
		// read table
		$lockingTable = $this->store->readBatch('locking', array(), $this->batchId);

		// update table
		if (array_key_exists($workerId, $lockingTable)) {
			$lockingTable[$workerId] = 'finished';	
		} else {
			return false;
		}
		
		// write table back
		$this->store->writeBatch('locking', $lockingTable);
		return true;
	}

	public function state()
	{
		return $this->state;
	}

	public function setState($state)
	{
		if ($state <> 'edit' 
			&& $state <> 'active' 
			&& $state <> 'post') return false;

		// delete all data when changing from edit to active state
		if ($this->state == 'edit' && $state == 'active') {
			$this->store->deleteAllWorkers($this->batchId);
		}

		$this->state = $state;
		$this->store->writeBatch('state', $state, $this->batchId);
	}

	public function getWorker($wid)
	{
		$store = new DataStore();
		$meta = $store->readWorker('meta', null, $this->batchId, $wid);
		if (is_array($meta)) {
    		$meta['stepId'] = $store->readWorker('stepId', null, $this->batchId, $wid);
    		$meta['finished'] = ($meta['stepId'] == $this->countSteps() - 1);
    	}

    	return $meta;
	}

	public function workers($includeResults = false)
	{
		$store = new DataStore();
		$workers = array();
		$path = DATA_PATH . $this->batchId . DS . 'workers' . DS;
		$files = glob($path . '*', GLOB_MARK);
	    foreach ($files as $file) 
	    {
	    	$file = preg_replace('#^' . preg_quote($path) . '#', '', $file);
	    	$wid = preg_replace('#'.DSX.'$#', '', $file);

	    	$meta = $store->readWorker('meta', null, $this->batchId, $wid);
	    	$meta['stepId'] = $store->readWorker('stepId', null, $this->batchId, $wid);
    		$meta['finished'] = ($meta['stepId'] == $this->countSteps() - 1);
    		$workers[$wid] = $meta;

	    	if ($includeResults) {
	    		$results = $store->readWorkerCSV('results', $this->batchId, $wid);
	    		$durations = array();

	    		// calculate durations
	    		if (is_array($results))
	    		{
	    			$lastTimestamp = $meta['timestamp'];
					foreach($results as $stepId => &$stepResults)
					{
						$durations[$stepId] = $stepResults[2] - $lastTimestamp;
						$lastTimestamp = $stepResults[2];
					}
				}

				$workers[$wid]['durations'] = $durations;
				$workers[$wid]['results'] = $results;
	    	}
	    }

	    return $workers;
	}

	public function resultsPerStep()
	{
		$workers = $this->workers(true);
		$steps = array();

		foreach($workers as $wid => $w)
		{	
			if (!is_array($w['results'])) continue;

			foreach($w['results'] as $stepId => $stepResults)
			{
				$steps[$stepId]['command'] = $stepResults[1];
				
				array_shift($stepResults);
				array_shift($stepResults);
				array_shift($stepResults);
				$steps[$stepId]['results'][$wid] = $stepResults;
			}

			foreach($w['durations'] as $stepId => $duration)
			{
				$steps[$stepId]['durations'][$wid] = $duration;
			}
		}

		// process durations
		foreach($steps as $stepId => &$step)
		{
			$sum = 0;
			$max = 0;
			$min = time();

			foreach($step['durations'] as $wid => $duration)
			{
				if ($duration > $max) $max = $duration;
				if ($duration < $min) $min = $duration;
				$sum += $duration;
			}

			$step['duration-avg'] = $sum / count($step['durations']);
			$step['duration-max'] = $max;
			$step['duration-min'] = $min;
		}

		// consolidate results part 1 - average, min, max
		foreach($steps as $stepId => &$step)
		{
			$sum = 0;
			$max = -0xffffffff;
			$min = 0xffffffff;
			$cnt = 0;

			foreach($step['results'] as $wid => $result)
			{
				if (count($result) == 0) continue;

				$value = $result[0];
				if ($value > $max) $max = $value;
				if ($value < $min) $min = $value;
				$sum += $value;
				$cnt ++;
			}

			$step['results-cnt'] = $cnt;
			if ($cnt > 0) {
				$step['results-avg'] = $sum / $cnt;
				$step['results-max'] = $max;
				$step['results-min'] = $min;
				$step['results-cnt'] = $cnt;
			}

			$step['workers'] = count($step['results']);
		}

		// consolidate results part 2 - standard deviation
		foreach($steps as $stepId => &$step)
		{
			$sd = 0;

			foreach($step['results'] as $wid => $result)
			{
				if (count($result) == 0) continue;

				$value = $result[0];
				$sd += ($step['results-avg'] - $value) * ($step['results-avg'] - $value);
			}

			if ($step['workers'] > 1) {
				$step['results-sd'] = sqrt($sd / ($step['workers'] - 1));
			} else {
				$step['results-sd'] = 0;
			}
		}

		return $steps;
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
	}

	private function collectMetaData()
	{
		$meta = array(
			'workerId' 		=> $this->registry->get('workerId'),
			'token' 		=> $this->registry->get('token'),
			'timestamp' 	=> time(),
			'remoteaddr' 	=> md5($_SERVER['REMOTE_ADDR']),
			'useragent' 	=> $_SERVER['HTTP_USER_AGENT'],
		);

		$this->store->writeWorker('meta', $meta);
	}
}