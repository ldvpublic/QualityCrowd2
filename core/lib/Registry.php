<?php

final class Registry
{
	private static $instance = null;
	private $data = null;

	private function __construct() 
	{
		$this->data = array();
	}
	private function __clone() {}

	public static function getInstance() 
	{
	   if (self::$instance === null) 
	   {
	       self::$instance = new self;
	   }
	   return self::$instance;
	}

	public function clear()
	{
		$this->data = array();
	}

	public function set($key, $value)
	{
		$this->data[$key] = $value;
	}

	public function get($key)
	{
		if (!array_key_exists($key, $this->data)) return null;
		return $this->data[$key];
	}

 }