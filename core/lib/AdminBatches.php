<?php

class AdminBatches extends AdminPage
{
	protected function prepareRender()
	{
		$this->tpl->set('batchlist', $this->renderBatchList());
	}

	private function renderBatchList()
	{
		$batches = $this->getBatches();
		ksort($batches);
		$o = '';
		
		foreach($batches as $batchId => $batch)
		{
			$rowTpl = new Template('admin.batches.row');
			$workers = $batch->workers();

			$rowTpl->set('id', $batchId);
			$rowTpl->set('state', $batch->state());
			$rowTpl->set('title', $batch->meta('title'));
			$rowTpl->set('steps', $batch->countSteps());
			$rowTpl->set('workers', count($workers));

			$finished = 0;
			foreach($workers as $w) {
				if ($w['finished']) $finished ++;
			}
			$rowTpl->set('finished', $finished);

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
	    	$file = preg_replace('#^' . preg_quote(BATCH_PATH) . '#', '', $file);
	    	$file = preg_replace('#/definition.qcs$#', '', $file);
	    	
	    	$myBatchCompiler = new BatchCompiler($file);
			$batches[$file] = $myBatchCompiler->getBatch();
	    }

	    return $batches;
	}
}
