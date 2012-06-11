<?php

class StepVideo extends Step
{
	protected function init() {}
	
	public function validate(&$data) 
	{
		$msg = array();

		if (!$this->properties['skipvalidation']) {
			if (!isset($data['watched']) || $data['watched'] <> true)
			{
				$msg[] = 'You have to watch the whole video.';
			}
			if (!isset($data['answered']) || $data['answered'] <> true) 
			{
				$msg[] = 'You have to answer the question.';
			}
		}

		if (count($msg) == 0)
		{
			unset($data['watched']);
			unset($data['answered']);
			$data = array('video' => $this->arguments[0]) + $data;
			return true;
		} else 
		{
			return $msg;
		}
	}

	protected function prepareRender()
	{
		// prerender video players
		$videos = array();

		foreach ($this->arguments as $video)
		{
			$tpl = new Template('player');
			$tpl->set('file', MEDIA_URL . $video);
			$tpl->set('width', 352);
			$tpl->set('height', 288);
			$videos[] = $tpl->render();
		}

		$this->tpl->set('videos', $videos);

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
