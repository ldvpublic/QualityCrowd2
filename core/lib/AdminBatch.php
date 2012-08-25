<?php

class AdminBatch extends AdminPage
{
	protected function prepareRender()
	{
		$batchId = $this->path[1];
		$this->tpl->set('id', $batchId);

		$myBatchCompiler = new BatchCompiler($batchId);
		if (!$myBatchCompiler->exists())
		{
			if (isset($this->path[2]) && $this->path[2] == 'new')
			{
				$myBatchCompiler->create();
			}
		}

		$batch = $myBatchCompiler->getBatch();

		if (isset($this->path[2])) 
		{	
			$this->tpl->set('subpage', $this->path[2]);

			switch($this->path[2])
			{
			case 'new':
			case 'edit':
				// save QC-Script
				if (isset($_POST['qcs'])) {
					$myBatchCompiler->setSource($_POST['qcs']);
				}

				// save state
				if (isset($_POST['state'])) {
					$batch->setState($_POST['state']);
				}

				$myTpl = new Template('admin.batch.edit');
				$myTpl->set('qcs', $myBatchCompiler->getSource());
				$myTpl->set('state', $batch->state());
				break;

			case 'validate':
				$myTpl = new Template('admin.batch.validate');

				$workerId = (isset($_GET['workerid']) ? $_GET['workerid'] : '');
				$workerId = preg_replace("/[^a-zA-Z0-9-]/", "", $workerId);
				$result = $batch->getWorker($workerId);
				$myTpl->set('result', $result);
				$myTpl->set('query', $workerId);
				break;

			case 'results':
				$myTpl = new Template('admin.batch.results');
				$myTpl->set('steps', $batch->steps());
				$myTpl->set('results', $batch->resultsPerStep());
				break;

			case 'results.csv':
				$myTpl = new Template('admin.batch.results.csv');
				$myTpl->set('batchId', $batchId);
				$myTpl->set('steps', $batch->steps());
				$myTpl->set('workers', $batch->workers(true));
				echo $myTpl->render();
				exit;
				break;

			case 'results.xlsx':
				$myTpl = new Template('admin.batch.results.xlsx');
				$myTpl->set('batchId', $batchId);
				$myTpl->set('steps', $batch->steps());
				$myTpl->set('workers', $batch->workers(true));
				echo $myTpl->render();
				exit;
				break;

			case 'browsers':
				$myTpl = new Template('admin.batch.browsers');
				$myTpl->set('workers', $batch->workers());
				break;

			default:
				if (is_numeric($this->path[2]))
				{
					$myTpl = new Template('admin.batch.preview');
					$stepId = $this->path[2];
					$step = $batch->getStepObject($stepId, 'WID');
					$myTpl->set('stepid', $stepId);
					$myTpl->set('preview', $step->render());
				}
				break;
			}
		} else {
			$this->tpl->set('subpage', '');
		}
		
		if (!isset($myTpl))
		{
			$myTpl = new Template('admin.batch.details');

			$myTpl->set('properties', $batch->meta());
			$myTpl->set('steps', $batch->steps());
			$myTpl->set('state', $batch->state());
		}

		$this->tpl->set('content', $myTpl->render());

	}
}
