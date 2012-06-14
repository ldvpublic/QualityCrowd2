<?php

class Admin extends Base
{
	private $tpl;
	private $username;

	public function __construct($username)
	{
		parent::__construct();

		$this->tpl = new Template('admin');
		$this->username = $username;
		$this->tpl->set('username', $username);
	}

	public function render()
	{
		$this->tpl->set('batchList', $this->renderBatchList());
		return $this->tpl->render();
	}

	private function renderBatchList()
	{
		$batches = $this->getBatches();
		$o = '';

		foreach($batches as $batchId => $batch)
		{
			$rowTpl = new Template('admin.batchrow');
			$workers = $batch->getWorkers();

			$rowTpl->set('id', $batchId);
			$rowTpl->set('title', $batch->getProperty('title'));
			$rowTpl->set('steps', $batch->countSteps());
			$rowTpl->set('workers', count($workers));
			//$rowTpl->set('finished', $batch->countSteps());

			$o .= $rowTpl->render();
		}

		return $o;
	}

	private function getBatches()
	{
		$batches = array();

		$files = glob(BATCH_PATH . '*/definition.qcs', GLOB_MARK);
	    foreach ($files as $file) 
	    {
	    	$file = preg_replace('#^' . BATCH_PATH . '#', '', $file);
	    	$file = preg_replace('#/definition.qcs$#', '', $file);
	    	
	    	$myBatchCompiler = new BatchCompiler($file);
			$batches[$file] = $myBatchCompiler->getBatch();
	    }

	    return $batches;
	}
}
