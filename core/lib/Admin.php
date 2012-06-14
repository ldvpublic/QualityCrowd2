<?php

class Admin extends AdminPage
{
	private $username;
	private $page;

	public function __construct($username, $page = 'batches')
	{
		parent::__construct();

		$this->username = $username;
		$this->tpl->set('username', $username);
		$this->page = $page;
	}

	public function prepareRender()
	{
		$class = 'Admin' . ucfirst($this->page);
		$pageObject = new $class($this->page);

		$this->tpl->set('content', $pageObject->render());

		return $this->tpl->render();
	}
}
