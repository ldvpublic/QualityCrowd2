<?php

class Admin extends AdminPage
{
	private $username;

	public function __construct($username, $path = null)
	{
		parent::__construct(null);
		$this->path = $path;

		$this->username = $username;
		$this->tpl->set('username', $username);	
	}

	public function prepareRender()
	{
		$class = 'Admin' . ucfirst($this->path[0]);
		$pageObject = new $class($this->path);

		$this->tpl->set('page', $this->path[0]);
		$this->tpl->set('content', $pageObject->render());

		return $this->tpl->render();
	}
}
