<?php

class AdminDoc extends AdminPage
{
	protected function prepareRender()
	{
		require(ROOT_PATH . 'core/3p/markdown.php');
		$md = file_get_contents(ROOT_PATH . 'core/doc/qc-script.md');
		$html = Markdown($md);
		$this->tpl->set('doc', $html);
	}
}
