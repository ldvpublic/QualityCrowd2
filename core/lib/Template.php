<?php

class Template
{
	private $name;
	private $scope;
	private static $fields;

	public function __construct($name, $scope = '')
	{
		if (!self::exists($name))
		{
			throw new Exception("Template \"$name\" not found");
		}

		$this->name = $name;
		$this->scope = $scope;

		if (!isset(self::$fields)) {
			self::$fields = array();
		}

		if (!isset(self::$fields[$this->scope])) {
			self::$fields[$this->scope] = array();
		}
	}

	public static function exists($name)
	{
		return file_exists(TEMPLATE_PATH . $name . '.tpl.php');
	}

	public function set($key, $value)
	{
		self::$fields[$this->scope][$key] = $value;
	}

	public function setArray($array) 
	{
		if (!is_array($array)) {
			throw new Exception("Parameter has to be an array");
		}

		self::$fields[$this->scope] = array_merge(self::$fields[$this->scope], $array);
	}

	public function render()
	{	
		foreach(self::$fields[$this->scope] as $key => $value)
		{
			$$key = $value;
		}
		
		ob_start();
		require_once('templateHelpers.php');
		require(TEMPLATE_PATH . $this->name . '.tpl.php');
		$o = ob_get_contents();
		ob_end_clean();

		return $o;
	}
}