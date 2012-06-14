<?php

class AdminBatch extends AdminPage
{
	protected function prepareRender()
	{
		$batchId = $this->path[1];
		$this->tpl->set('id', $batchId);

		$myBatchCompiler = new BatchCompiler($batchId);
		$batch = $myBatchCompiler->getBatch();

		$this->tpl->set('properties', $batch->meta());
		$this->tpl->set('steps', $batch->steps());

		$this->tpl->set('qcs', $myBatchCompiler->getSource());		
	}
}
