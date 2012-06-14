<?php

abstract class AdminPage extends Base
{
	protected $tpl;
	protected $path;
	
	abstract protected function prepareRender();

	public function __construct($path = null)
	{
		parent::__construct();

		$this->path = $path;

		$template = 'admin';
		if (!$this->path == null) $template .= '.' . $path[0];
		
		$this->tpl = new Template($template);
	}

	public function render()
	{		
		$this->prepareRender();
		return $this->tpl->render();
	}
}
