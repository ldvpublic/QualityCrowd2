<?php

abstract class Base
{
	protected $store;
	protected static $config;

	public function __construct()
	{
		if (get_class($this) <> 'DataStore')
			$this->store = new DataStore();

		if (!is_array(self::$config))
		{
			self::$config = require(ROOT_PATH . 'core'.DS.'config.php');
		}
	}

	public function __sleep() 
	{
		if (get_class($this) <> 'DataStore')
			$this->store = null;
	}
	
	public function __wakeup() 
	{
		if (get_class($this) <> 'DataStore')
			$this->store = new DataStore();
	}

	protected function getConfig($key)
	{
		return self::$config[$key];
	}
}