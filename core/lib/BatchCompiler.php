<?php

class BatchCompiler extends Base
{
	private $batchId;
	private $source;

	public static $syntax = array(
		// special commands
		'meta'          => array(
			'minArguments' => 1,
			'arguments' => array('key', 'value'),
			'description' => '',
			'keys' => array(
				'title' 		=> '',
				'description'	=> '',
				'workers'		=> -1,
				'timeout'		=> 600,
				),
			),
		'var'           => array(
			'minArguments' => 2,
			'arguments' => array('variable', 'value'),
			'description' => 'Sets an internal variable to `<value>`. To use this variable for example in a `set` command, use the following syntax: `set title $titlevar`',
			),
		'set'           => array(
			'minArguments' => 1,
			'arguments' => array('property', 'value'),
			'description' => 'The `set` command sets a property defined by the `<property>`-argument to the value specified by `<value>`. This property can be used by all further commands and its value will be set until a matching `unset`-command is processed.',
			),
		'unset'         => array(
			'minArguments' => 1,
			'arguments' => array('property'),
			'description' => 'Unsets the property with the passed `<property>`. If `all` is passed all properties will be unset.',
			),

		// step commands
		'page'          => array(
			'minArguments' => 0, 
			'arguments' => array(),
			'properties'   => array(
				'title' 		 => '',
				'text' 			 => '',
				),
			'description' => 'Displays a simple HTML-Page which is particularly useful as a welcome page. It is recommended to use the `include()`-macro to set the page text property (e.g. `set text include(welcome.html)`)',
			),
		'video' => array(
			'minArguments' => 1, 
			'arguments' => array('video1', 'video2'),
			'properties'   => array(
				'skipvalidation' => false,
				'title' 		 => '',
				'text' 			 => '',
				'question' 		 => '',
				'answermode'	 => 'discrete',
				'answers'		 => '1: Bad; 2: Poor; 3: Fair; 4: Good; 5: Excellent',
				'mediaurl' 		 => MEDIA_URL,
				'videowidth' 	 => 352,
				'videoheight' 	 => 288,
				),
			'description' => '',
			),
		'image' => array(
			'minArguments' => 1,
			'arguments' => array('image'),
			'properties'   => array(
				'skipvalidation' => false,
				'title' 		 => '',
				'text' 			 => '',
				'question' 		 => '',
				'answermode'	 => 'discrete',
				'answers'		 => '1: Bad; 2: Poor; 3: Fair; 4: Good; 5: Excellent',
				'mediaurl' 		 => MEDIA_URL,
				),
			'description' => '',
			),
		'question' => array(
			'minArguments' => 0,
			'arguments' => array(),
			'properties'   => array(
				'skipvalidation' => false,
				'title' 		 => '',
				'text' 			 => '',
				'question' 		 => '',
				'answermode'	 => 'discrete',
				'answers'		 => '1: First answer; 2: Second answer; 3: Third answer',
				),
			'description' => '',
			),
		'showtoken' => array(
			'minArguments' => 0,
			'arguments' => array(),
			'properties'   => array(
				'title' 		 => '',
				'text' 			 => '',
				),
			'description' => '',
			),
		'qualification' => array(
			'minArguments' => 1,
			'arguments' => array('qualification-batch'),
			'properties'   => array(),
			'description' => '',
			),
		);

	public function __construct($batchId) 
	{
		$this->batchId = $batchId;
	}

	public function getSource() 
	{
		// load source file
		if ($this->source == '')
			$this->source = file_get_contents($this->getSourceFileName());

		return $this->source;
	}

	public function setSource($source)
	{
		if ($source <> '')
		{
			$this->source = $source;
			$file = $this->getSourceFileName();
			file_put_contents($file, $source);
			chmod($file, $this->getConfig('filePermissions'));
		}
	}

	public function exists()
	{
		return file_exists($this->getSourceFileName());
	}

	public function create()
	{
		$defaultQCS = <<<'EOT'
meta title "New Batch"
meta description "New batch description"

set title "New Batch"
set text "Hello World"
page

EOT;
		$path = BATCH_PATH . $this->batchId;
		$file = $path . DS . 'definition.qcs';

		mkdir($path);
		chmod($path, $this->getConfig('dirPermissions'));

		file_put_contents($file, $defaultQCS); 
		chmod($file, $this->getConfig('filePermissions'));
	}

	public function getBatch()
	{
		if (!$this->exists())
		{
			throw new Exception('Batch with id "' . $this->batchId . '" not found');
		}

		$myBatch = null;

		if (!file_exists($this->getCacheFileName()) ||
			filemtime($this->getSourceFileName()) > filemtime($this->getCacheFileName()))
		{
			$myBatch = $this->compile();
			$myBatch2 = clone $myBatch;
			$file = $this->getCacheFileName();
			file_put_contents($file, serialize($myBatch2));
			chmod($file, $this->getConfig('filePermissions'));
		} else
		{
			$myBatch = file_get_contents($this->getCacheFileName());
			$myBatch = unserialize($myBatch);
		}

		return $myBatch;
	}

	private function compile() 
	{
		$batchSteps = array();
		$sourceData = $this->parse();

		$meta = array();
		$stepProperties = array();
		$variables = array();

		foreach($sourceData as $sourceStep) 
		{
			$batchStep = array(
				'command', 
				'arguments' => array(), 
				'properties' => array()
				);
			
			switch($sourceStep['command']) 
			{
				case 'meta':
					$meta[$sourceStep['arguments'][0]] = 
						$this->parseValue($sourceStep['arguments'][1], $variables);
					break;

				case 'set':
					$value = (isset($sourceStep['arguments'][1]) ? $sourceStep['arguments'][1] : true);
					$stepProperties[$sourceStep['arguments'][0]] = 
						$this->parseValue($value, $variables);
					break;

				case 'var':
					$variables[$sourceStep['arguments'][0]] = 
						$this->parseValue($sourceStep['arguments'][1], $variables);
					break;

				case 'unset':
					if ($sourceStep['arguments'][0] == 'all')
					{
						unset($stepProperties[$sourceStep['arguments'][0]]);	
					} else
					{
						$stepProperties = array();
					}
					break;

				default:
					$batchStep['command'] = $sourceStep['command'];

					// set properties
					foreach(self::$syntax[$sourceStep['command']]['properties'] as $property => $default)
					{
						if (isset($stepProperties[$property])) {
							$batchStep['properties'][$property] = $stepProperties[$property];
						} else {
							$batchStep['properties'][$property] = $default;
						}
					}

					// set arguments
					$batchStep['arguments'] = array();
					foreach($sourceStep['arguments'] as $arg) 
					{
						$batchStep['arguments'][] = $this->parseValue($arg, $variables);
					}

					$batchSteps[] = $batchStep;
					break;
			}
		}

		// clean up meta properties
		foreach(self::$syntax['meta']['keys'] as $property => $default)
		{
			if (!isset($meta[$property])) {
				$meta[$property] = $default;
			}
		}
		
		$myBatch = new Batch($this->batchId, $meta, $batchSteps);

		return $myBatch;
	}

	private function parse() 
	{
		$data = array();
		$source = $this->getSource();
		$source = $this->normalize($source);

		// parse source file
		$lines = explode("\n", $source);
		foreach($lines as $line)
		{
			$words = explode(' ', $line);
			$words = str_getcsv($line, ' ', '"');
			
			if (!isset(self::$syntax[$words[0]]))
			{
				throw new Exception ($this->batchId . ': unknown command "' . $words[0] . '"');
			}
			$cmd = self::$syntax[$words[0]];

			if (count($words) < $cmd['minArguments'] + 1) 
			{
				throw new Exception($this->batchId . ': ' .
					'"' . $words[0] . '" requires at least ' . 
					$cmd['minArguments'] . ' arguments');
			}

			if (count($words) > count($cmd['arguments']) + 1) 
			{
				throw new Exception($this->batchId . ': ' .
					'"' . $words[0] . '" accepts a maximum of ' . 
					count($cmd['arguments']) . ' arguments');
			}

			$data[] = array(
				'command' => $words[0],
				'arguments' => array_slice($words, 1),
				);
		}

		return $data;
	}

	private function normalize($source)
	{
		// remove comments
		$source = preg_replace("/^\s*#.*$/m", '', $source);

		// clean up line endings
		$source = str_replace("\r\n", "\n", $source);
		$source = preg_replace("/\n{2,}/", "\n", $source);
		$source = preg_replace("/\n$/", '', $source);
		$source = preg_replace("/^\n/", '', $source);

		// replace tabs with spaces
		$source = str_replace("\t", ' ', $source);

		// remove multiple spaces
		$source = preg_replace("/\ {2,}/", ' ', $source);

		// remove spaces at line endings
		$source = preg_replace("/\ *\n/", "\n", $source);

		return $source;
	}

	private function parseValue($value, $variables)
	{
		// leave ints, bools, etc. untouched
		if (gettype($value) <> 'string') return $value;

		// resolve variables
		foreach($variables as $k => $v)
		{
			$value = str_replace('$' . $k, $v, $value);
		}

		// find and resolve includes
		if (preg_match('/^include\(\s*(.+)\s*\)$/', $value, $matches))
		{
			$inc = $matches[1];
			$inc = str_replace('/', DS, $inc);
			$inc = str_replace('\\', DS, $inc);
			
			$file = BATCH_PATH . $this->batchId . DS . $inc;
			if (file_exists($file))
			{
				$value = file_get_contents($file);
			}
		}

		return $value;	
	}

	private function getSourceFileName()
	{
		return BATCH_PATH . $this->batchId . DS .'definition.qcs';
	}

	private function getCacheFileName()
	{
		return TMP_PATH . 'batch-cache' . DS . $this->batchId . '.txt';
	}
}