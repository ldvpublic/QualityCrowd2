<?php

class StepQuestion extends Step
{
	protected function init() 
	{
		
	}

	public function validate(&$data) 
	{
		$msg = array();

		if (!$this->properties['skipvalidation']) {
			if ($this->command == 'video' && 
				(!isset($data['watched']) || $data['watched'] <> true))
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
			$data['media'] = $this->arguments[0];
			return true;
		} else 
		{
			return $msg;
		}
	}

	protected function prepareRender()
	{
		switch($this->command)
		{
			case 'video': $this->prepareVideo(); break;
			case 'image': $this->prepareImage(); break;
		}

		$this->prepareAnswers();
	}

	private function prepareImage()
	{
		$img = $this->arguments[0];
		$this->tpl->set('image', $this->getMediaUrl() . $img);
	}

	private function prepareVideo()
	{
		// prerender video players
		$videos = array();

		foreach ($this->arguments as $video)
		{
			$tpl = new Template('player');
			$tpl->set('file', $this->getMediaUrl() . $video);
			$tpl->set('filename', $video);
			$tpl->set('width', 352);
			$tpl->set('height', 288);
			$videos[$video] = $tpl->render();
		}

		$this->tpl->set('videos', $videos);
	}

	private function prepareAnswers()
	{
		// parse answers
		$answerStr = $this->properties['answers'];
		$answerStr = explode(';', $answerStr);

		$answers = array();
		foreach ($answerStr as $str)
		{
			$str = explode(':', $str);

			$answers[] = array(
				'value' => trim($str[0]),
				'text' => trim($str[1]),
			);
		}

		// set answer template
		$answermode = $this->properties['answermode'];
		if (!Template::exists('answer.' . $answermode)) {
			$answermode = 'continous';
		}

		$tpl = new Template('answer.' . $answermode);
		$tpl->set('answers', $answers);
		$answerform = $tpl->render();
		$this->tpl->set('answerform', $answerform);
	}

	private function getMediaUrl()
	{
		$mediaUrl = MEDIA_URL;
		if (isset($this->properties['mediaurl']) && $this->properties['mediaurl'] <> '')
		{
			$mediaUrl = $this->properties['mediaurl'];
		}

		return $mediaUrl;
	}
}
