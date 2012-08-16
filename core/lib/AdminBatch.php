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
			switch($this->path[2])
			{
			case 'new':
			case 'edit':
				// save QC-Script
				if (isset($_POST['qcs']))
				{
					$myBatchCompiler->setSource($_POST['qcs']);
				}

				$myTpl = new Template('admin.batch.edit');
				$myTpl->set('qcs', $myBatchCompiler->getSource());
				break;

			case 'results':
				$myTpl = new Template('admin.batch.results');
				$myTpl->set('steps', $batch->resultsPerStep());
				break;

			default:
				if (is_numeric($this->path[2]))
				{
					$myTpl = new Template('admin.batch.preview');
					$stepId = $this->path[2];
					$myTpl->set('stepid', $stepId);
					$myTpl->set('preview', $batch->renderStep($stepId));
				}
				break;
			}
		}
		
		if (!isset($myTpl))
		{
			$myTpl = new Template('admin.batch.details');

			$myTpl->set('properties', $batch->meta());
			$myTpl->set('steps', $batch->steps());
		}
		$this->tpl->set('content', $myTpl->render());	
	}
}
