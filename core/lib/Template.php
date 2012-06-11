<?php

class Template
{
	private $name;
	private static $fields;

	public function __construct($name)
	{
		if (!self::exists($name))
		{
			throw new Exception("Template \"$name\" not found");
		}

		$this->name = $name;

		if (!isset(self::$fields))
		{
			self::$fields = array();
		}
	}

	public static function exists($name)
	{
		return file_exists(TEMPLATE_PATH . $name . '.tpl.php');
	}

	public function set($key, $value)
	{
		self::$fields[$key] = $value;
	}

	public function setArray($array) 
	{
		if (!is_array($array))
		{
			throw new Exception("Parameter has to be an array");
		}
		self::$fields = self::$fields + $array;
	}

	public function render()
	{	
		foreach(self::$fields as $key => $value)
		{
			$$key = $value;
		}
		
		ob_start();			
		require(TEMPLATE_PATH . $this->name . '.tpl.php');
		$o = ob_get_contents();
		ob_end_clean();

		return $o;
	}
}