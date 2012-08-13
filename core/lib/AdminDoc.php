<?php

class AdminDoc extends AdminPage
{
	protected function prepareRender()
	{
		require(ROOT_PATH . 'core'.DS.'3p'.DS.'markdown.php');
		$md = file_get_contents(ROOT_PATH . 'core'.DS.'doc'.DS.'qc-script.md');
		$html = Markdown($md);
		$this->tpl->set('doc', $html);
	}
}
