<?php

// this class handles the step commands "video", "image" and "question"

class ElementQuestion extends StepElement
{
	protected function init() 
	{
		
	}

	public function validate(&$data) 
	{
		$msg = array();

		if (!isset($data['answered-' . $this->uid]) || $data['answered-' . $this->uid] <> true) {
			$msg[] = 'You have to answer the question.';
		}
		
		if (count($msg) == 0) {
			unset($data['answered-' . $this->uid]);
			return true;
		} else  {
			return $msg;
		}
	}

	protected function prepareRender()
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

		$tpl = new Template('answer.' . $answermode, $this->step->batch()->id());
		$tpl->set('answers', $answers);
		$answerform = $tpl->render();
		$this->tpl->set('answerform', $answerform);
	}
}
