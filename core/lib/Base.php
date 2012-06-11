<?php

abstract class Base
{
	protected $registry;
	protected $store;

	public function __construct()
	{
		$this->registry = Registry::getInstance();
		$this->store = new DataStore();
	}

	public function __sleep() 
	{
		$this->registry = null;
		$this->store = null;
	}
	
	public function __wakeup() 
	{
		$this->registry = Registry::getInstance();
		$this->store = new DataStore();
	}
}