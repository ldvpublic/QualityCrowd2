<?php

class AdminDoc extends AdminPage
{
	protected function prepareRender()
	{
		$subpage = (isset($this->path[1]) ? $this->path[1] : '');
		$this->tpl->set('subpage', $subpage);

		require(ROOT_PATH . 'core'.DS.'3p'.DS.'markdown'.DS.'markdown.php');

		switch($subpage)
		{
			default:
			case '':
				$md = file_get_contents(ROOT_PATH . 'core'.DS.'doc'.DS.'qc-script.md');
				$html = Markdown($md);
				$this->tpl->set('content', $html);
				break;

			case 'reference':
				$myTpl = new Template('admin.doc.reference');
				$myTpl->set('syntax', BatchCompiler::$syntax);
				$this->tpl->set('content', $myTpl->render());
				break; 
		}
	}
}
