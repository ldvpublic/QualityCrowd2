<?php

class ElementVideo extends StepElement
{
	protected function init() 
	{
		
	}

	public function validate(&$data) 
	{
		$msg = array();
		
		if (!isset($data['watched-' . $this->uid]) || $data['watched-' . $this->uid] <> true) {
			$msg[] = 'You have to watch the whole video.';
		}

		if (count($msg) == 0) {
			unset($data['watched-' . $this->uid]);
			$data['media-' . $this->uid] = (isset($this->arguments['video1']) ? $this->arguments['video1'] : null);
			return true;
		} else {
			return $msg;
		}
	}

	protected function prepareRender()
	{
		// prerender video players
		$videos = array();

		foreach ($this->arguments as $video)
		{
			$tpl = new Template('player', $this->step->batch()->id());
			$tpl->set('file', $this->properties['mediaurl'] . $video);
			$tpl->set('filename', $video);
			$tpl->set('width',  $this->properties['videowidth']);
			$tpl->set('height', $this->properties['videoheight']);
			$videos[$video] = $tpl->render();
		}

		$this->tpl->set('videos', $videos);
	}
}
