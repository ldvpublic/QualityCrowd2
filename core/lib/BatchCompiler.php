<?php

class BatchCompiler
{
	private $batchId;
	private $commands;
	private $source;

	public function __construct($batchId) 
	{
		$this->batchId = $batchId;

		$this->commands = array(
			'meta'          => array('minArguments' => 1, 'maxArguments' => 2),
			'set'           => array('minArguments' => 1, 'maxArguments' => 2),
			'var'           => array('minArguments' => 2, 'maxArguments' => 2),
			'unset'         => array('minArguments' => 1, 'maxArguments' => 1),
			'page'          => array('minArguments' => 1, 'maxArguments' => 1),
			'video'         => array('minArguments' => 1, 'maxArguments' => 2),
			'image'         => array('minArguments' => 1, 'maxArguments' => 1),
			'question'      => array('minArguments' => 0, 'maxArguments' => 0),
			'showtoken'     => array('minArguments' => 0, 'maxArguments' => 0),
			'qualification' => array('minArguments' => 1, 'maxArguments' => 1),
			'return'        => array('minArguments' => 0, 'maxArguments' => 0),
			);
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
			file_put_contents($this->getSourceFileName(), $source);
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
page "Hello World"

EOT;
		$path = BATCH_PATH . $this->batchId;
		mkdir($path);
		file_put_contents($path . '/definition.qcs', $defaultQCS); 
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
			file_put_contents($this->getCacheFileName(), serialize($myBatch2));
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
			$batchStep = array();
			switch($sourceStep['command']) 
			{
				case 'meta':
				$meta[$sourceStep['arguments'][0]] = 
					$this->parseValue($sourceStep['arguments'][1], $variables);
				break;

				case 'set':
				$stepProperties[$sourceStep['arguments'][0]] = 
					$this->parseValue($sourceStep['arguments'][1], $variables);
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
				$batchStep['properties'] = $stepProperties;
				$batchStep['arguments'] = array();
				foreach($sourceStep['arguments'] as $arg)
				{
					$batchStep['arguments'][] = $this->parseValue($arg, $variables);
				}
				$batchSteps[] = $batchStep;
			}
		}

		$myBatch = new Batch($this->batchId, $meta, $batchSteps);
		$myBatch->renderStep(0);

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
			
			if (!isset($this->commands[$words[0]]))
			{
				throw new Exception ('unknown command "' . $words[0] . '"');
			}
			$cmd = $this->commands[$words[0]];

			if (count($words) < $cmd['minArguments'] + 1) 
			{
				throw new Exception(
					'"' . $words[0] . '" requires at least ' . 
					$cmd['minArguments'] . ' arguments');
			}

			if (count($words) > $cmd['maxArguments'] + 1) 
			{
				throw new Exception(
					'"' . $words[0] . '" accepts a maximum of ' . 
					$cmd['maxArguments'] . ' arguments');
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

		return $source;
	}

	private function parseValue($value, $variables)
	{
		// resolve variables
		foreach($variables as $k => $v)
		{
			$value = str_replace('$' . $k, $v, $value);
		}

		// find and resolve includes
		if (preg_match('/^include\(\s*(.+)\s*\)$/', $value, $matches))
		{
			$inc = $matches[1];
			
			$file = BATCH_PATH . $this->batchId . '/' . $inc;
			if (file_exists($file))
			{
				$value = file_get_contents($file);
			}
		}

		return $value;	
	}

	private function getSourceFileName()
	{
		return BATCH_PATH . $this->batchId . '/definition.qcs';
	}

	private function getCacheFileName()
	{
		return TMP_PATH . 'batch-cache/' . $this->batchId . '.txt';
	}
}