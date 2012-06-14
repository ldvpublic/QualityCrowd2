<?php

abstract class AdminPage extends Base
{
	protected $tpl;
	
	abstract protected function prepareRender();

	public function __construct($page = '')
	{
		parent::__construct();

		$template = 'admin';
		if ($page <> '') $template .= '.' . $page;
		$this->tpl = new Template($template);
	}

	public function render()
	{		
		$this->prepareRender();
		return $this->tpl->render();
	}
}
