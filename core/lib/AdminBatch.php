<?php

class AdminBatch extends AdminPage
{
	protected function prepareRender()
	{
		$batchId = $this->path[1];
		$this->tpl->set('id', $batchId);

		$myBatchCompiler = new BatchCompiler($batchId);
		$batch = $myBatchCompiler->getBatch();

		if (isset($this->path[2])) 
		{
			$myTpl = new Template('admin.batch.preview');
			$stepId = $this->path[2];
			$myTpl->set('stepid', $stepId);
			$myTpl->set('preview', $batch->renderStep($stepId));
		} else
		{
			$myTpl = new Template('admin.batch.details');

			$myTpl->set('properties', $batch->meta());
			$myTpl->set('steps', $batch->steps());
			$myTpl->set('qcs', $myBatchCompiler->getSource());
		}
		$this->tpl->set('content', $myTpl->render());	
	}
}
