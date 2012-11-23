<?php

class BatchCompiler extends Base
{
	private $batchId;
	private $source;

	public static $syntax = array(
		// special commands
		'meta' => array(
			'isBlock' => false,
			'needsBlock' => false,
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
		'var' => array(
			'isBlock' => false,
			'needsBlock' => false,
			'minArguments' => 2,
			'arguments' => array('variable', 'value'),
			'description' => 'Sets an internal variable to `<value>`. To use this variable for example in a `set` command, use the following syntax: `set title $titlevar`',
			),
		'set' => array(
			'isBlock' => false,
			'needsBlock' => false,
			'minArguments' => 1,
			'arguments' => array('property', 'value'),
			'description' => 'The `set` command sets a property defined by the `<property>`-argument to the value specified by `<value>`. This property can be used by all further commands and its value will be set until a matching `unset`-command is processed.',
			),
		'unset' => array(
			'isBlock' => false,
			'needsBlock' => false,
			'minArguments' => 1,
			'arguments' => array('property'),
			'description' => 'Unsets the property with the passed `<property>`. If `all` is passed all properties will be unset.',
			),
		'end' => array(
			'isBlock' => false,
			'needsBlock' => false,
			'minArguments' => 1,
			'arguments' => array('block'),
			'description' => 'TODO',
			),

		// blocks
		'step' => array(
			'isBlock' => true,
			'needsBlock' => false,
			'minArguments' => 0, 
			'arguments' => array('name'),
			'properties' => array(
				'delay' 		 => 0,
				'skipvalidation' => false,
				),
			'description' => 'TODO',
			),


		// commands inside blocks
		'title' => array(
			'isBlock' => false,
			'needsBlock' => true,
			'minArguments' => 1, 
			'arguments' => array('title'),
			'properties' => array(),
			'description' => 'Todo',
			),
		'text' => array(
			'isBlock' => false,
			'needsBlock' => true,
			'minArguments' => 1, 
			'arguments' => array('text'),
			'properties' => array(),
			'description' => 'Displays some text. For longer texts it is recommended to use the `include()`-macro: (e.g. `text include(welcome.html)`)',
			),
		'video' => array(
			'isBlock' => false,
			'needsBlock' => true,
			'minArguments' => 1, 
			'arguments' => array('video1', 'video2'),
			'properties' => array(
				'mediaurl' 		 => MEDIA_URL,
				'videowidth' 	 => 352,
				'videoheight' 	 => 288,
				),
			'description' => '',
			),
		'image' => array(
			'isBlock' => false,
			'needsBlock' => true,
			'minArguments' => 1,
			'arguments' => array('image'),
			'properties' => array(	
				'mediaurl' 		 => MEDIA_URL,
				),
			'description' => '',
			),
		'question' => array(
			'isBlock' => false,
			'needsBlock' => true,
			'minArguments' => 1,
			'arguments' => array('question'),
			'properties'   => array(
				'answermode'	 => 'discrete',
				'answers'		 => '1: First answer; 2: Second answer; 3: Third answer',
				),
			'description' => '',
			),
		'qualification' => array(
			'isBlock' => false,
			'needsBlock' => true,
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

step
	title "New Batch"
	text "Hello World"
end step

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
			filemtime($this->getSourceFileName()) > filemtime($this->getCacheFileName()) ) //|| true)
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
		$steps = array();
		$sourceData = $this->parse();

		$meta = array();
		$properties = array('global' => array(), 'step' => array());
		$variables = array('global' => array(), 'step' => array());

		$currentScope = 'global';

		foreach($sourceData as $sourceStep) 
		{
			switch($sourceStep['command']) 
			{
				case 'meta':
					$meta[$sourceStep['arguments'][0]] = 
						$this->parseValue($sourceStep['arguments'][1], $variables[$currentScope]);
					break;

				case 'set':
					$value = (isset($sourceStep['arguments'][1]) ? $sourceStep['arguments'][1] : true);
					$properties[$currentScope][$sourceStep['arguments'][0]] = 
						$this->parseValue($value, $variables[$currentScope]);
					break;

				case 'var':
					$variables[$currentScope][$sourceStep['arguments'][0]] = 
						$this->parseValue($sourceStep['arguments'][1], $variables[$currentScope]);
					break;

				case 'unset':
					if ($sourceStep['arguments'][0] == 'all') {
						$properties[$currentScope] = array();
					} else
					{
						unset($properties[$currentScope][$sourceStep['arguments'][0]]);	
					}
					break;

				case 'step':
					$step = array(
						'arguments' => array(),
						'properties' => array(),
					 	'elements' => array()
					 	);

					// set properties
					foreach(self::$syntax['step']['properties'] as $property => $default)
					{
						if (isset($properties['global'][$property])) {
							$step['properties'][$property] = $properties['global'][$property];
						} else {
							$step['properties'][$property] = $default;
						}
					}

					// set arguments
					$i = 0;
					foreach($sourceStep['arguments'] as $arg) 
					{
						$argumentKey = self::$syntax['step']['arguments'][$i];
						$step['arguments'][$argumentKey] = $this->parseValue($arg, $variables['global']);
						$i++;
					}

					$properties['step'] = $properties['global'];
					$variables['step'] = $variables['global'];
					$currentScope = 'step';
					break;

				case 'end':
					$steps[] = $step;
					$currentScope = 'global';
					break;

				default:
				
					$element = array(
						'command' => $sourceStep['command'],
						'arguments' => array(),
						'properties' => array(),
						);

					// set properties
					foreach(self::$syntax[$sourceStep['command']]['properties'] as $property => $default)
					{
						if (isset($properties['step'][$property])) {
							$element['properties'][$property] = $properties['step'][$property];
						} else {
							$element['properties'][$property] = $default;
						}
					}

					// set arguments
					$element['arguments'] = array();
					$i = 0;
					foreach($sourceStep['arguments'] as $arg) 
					{
						$argumentKey = self::$syntax[$sourceStep['command']]['arguments'][$i];
						$element['arguments'][$argumentKey] = $this->parseValue($arg, $variables['step']);
						$i++;
					}

					$step['elements'][] = $element;
					
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
		
		$myBatch = new Batch($this->batchId, $meta, $steps);

		return $myBatch;
	}

	private function parse() 
	{
		$data = array();
		$source = $this->getSource();
		$source = $this->normalize($source);
		$source = $this->resolveMacros($source);
		$source = $this->resolveForLoops($source);

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

	private function resolveMacros($source) 
	{
		// find macro definitions
		$macros = $this->extractBlock($source, 'macro');
	
		// replace macro references
		foreach($macros as $macro) {
			$content = implode("\n", $macro['content']);
			$source = str_replace('$' . $macro['arguments'][0], $content, $source);
		}
		
		return $source;
	}

	private function resolveForLoops($source)
	{
		// find list definitions
		$lists = $this->extractBlock($source, 'list');
		
		// find for loops
		$forloops = $this->extractBlock($source, 'for');
		//header("Content-Type: text/plain; charset=utf8");

		// expand for loops
		$lines = explode("\n", $source);

		foreach($forloops as $loop) {
			// find matching list
			$myList = null;
			foreach($lists as $list) {
				if ($list['arguments'][0] == $loop['arguments'][2]) {
					$myList = $list['content'];
					break;
				}
			}
			if ($myList === null) {
				throw new Exception("List with name '{$loop['arguments'][2]}' not found.");
			}

			// expanding
			$loopContent = array();
			foreach($myList as $listItem) {
				$content = implode("\n", $loop['content']);	
				$content = str_replace('$' . $loop['arguments'][0], $listItem, $content);
				$loopContent[] = $content;
			}

			// replacing
			$loopContent = implode("\n", $loopContent);	
			$lines[$loop['start']] = $loopContent;
		}
		
		$source = implode("\n", $lines);

		return $source;
	}

	private function extractBlock(&$source, $keyword)
	{
		$blocks = array();

		$insideBlock = false;
		$lines = explode("\n", $source);
		foreach($lines as $li => $line)
		{
			$words = explode(' ', $line);
			$words = str_getcsv($line, ' ', '"');

			if ($words[0] == $keyword) {
				array_shift($words);
				$block = array(
					'start' => $li,
					'arguments'  => $words,
					'content' => array(),
					);
				$insideBlock = true;

			} elseif ($words[0] == 'end' && $words[1] == $keyword) {
				$block['length'] = $li - $block['start'] + 1;
				$blocks[] = $block;
				$insideBlock = false; 
			} else {
				if ($insideBlock) {
					$block['content'][] = $line;
				}
			}
		}

		// remove block definition
		foreach($blocks as $block) {
			$replacement = array_fill(0, $block['length'], '');
			array_splice($lines, $block['start'], $block['length'], $replacement);	
		}

		$source = implode("\n", $lines);

		return $blocks;
	}

	private function normalize($source)
	{
		// remove comments
		$source = preg_replace("/^\s*#.*$/m", '', $source);

		// replace tabs with spaces
		$source = str_replace("\t", ' ', $source);

		// clean up line endings
		$source = str_replace("\r\n", "\n", $source);

		// remove empty lines
		$source = preg_replace('/^\s*$/m', '', $source);
		$source = str_replace("\n\n", "\n", $source);
		$source = preg_replace('/^\n/', "", $source);
		$source = preg_replace('/\n$/', "", $source);

		// remove multiple spaces
		$source = preg_replace("/\ {2,}/", ' ', $source);

		// remove spaces at line beginnings
		$source = preg_replace("/^\ /m", "", $source);

		// remove spaces at line endings
		$source = preg_replace('/\ *\n/', "\n", $source);

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