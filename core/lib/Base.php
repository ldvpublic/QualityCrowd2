<?php

abstract class Base
{
	protected $registry;
	protected $store;
	protected static $config;

	public function __construct()
	{
		$this->registry = Registry::getInstance();
		$this->store = new DataStore();

		if (!is_array(self::$config))
		{
			self::$config = require(ROOT_PATH . 'core/config.php');
		}
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

	protected function getConfig($key)
	{
		return self::$config[$key];
	}
}