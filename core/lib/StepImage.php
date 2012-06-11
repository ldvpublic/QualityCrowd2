<?php

class StepImage extends Step
{
	protected function init() {}
	
	public function validate(&$data) 
	{
		$msg = array();

		if (!$this->properties['skipvalidation']) {
			if (!isset($data['answered']) || $data['answered'] <> true) 
			{
				$msg[] = 'You have to answer the question.';
			}
		}

		if (count($msg) == 0)
		{
			unset($data['answered']);
			$data = array('image' => $this->arguments[0]) + $data;
			return true;
		} else 
		{
			return $msg;
		}
	}

	protected function prepareRender()
	{

		$img = $this->arguments[0];
		$this->tpl->set('image', MEDIA_URL . $img);

		// set answer template
		$answermode = $this->properties['answermode'];
		if (!Template::exists('answer-' . $answermode)) {
			$answermode = 'continous';
		}

		// prerender answer-form
		$tpl = new Template('answer-' . $answermode);
		$answerform = $tpl->render();
		$this->tpl->set('answerform', $answerform);

	}
}
