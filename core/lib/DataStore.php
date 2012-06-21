<?php

class DataStore extends Base
{
	public function write($filename, $data)
	{
		$file = $this->getDataPath() . $filename . '.txt';
		$data = serialize($data);
		file_put_contents($file, $data);
		chmod($file, $this->getConfig('filePermissions'));
	}

	public function read($filename, $default = null, $batchId = '')
	{
		$file = $this->getDataPath($batchId) . $filename . '.txt';
		if (file_exists($file))
		{
			$data = file_get_contents($file);
			$data = unserialize($data);
			return $data;
		} else
		{
			return $default;
		}
	}

	public function writeCSV($filename, $data)
	{
		$file = $this->getDataPath() . $filename . '.csv';
		$fh = fopen($file, 'a');
		foreach($data as $row)
		{
			fputcsv($fh, $row);
		}
		fclose($fh);
		chmod($file, $this->getConfig('filePermissions'));
	}

	public function delete($file = '')
	{
		$path = $this->getDataPath();

		if ($file == '')
		{
		    $files = glob($path . '*', GLOB_MARK);
		    foreach ($files as $file) 
		    {
		        unlink($file);
		    }
		    rmdir($path);
		} else
		{
			$file = $path . $file;
			if (file_exists($file))
			{
				unlink($file);
			}
		}
	}

	private function getDataPath($batchId = '') 
	{
		if ($batchId == '')
		{
			$batchId = $this->registry->get('batchId');
		}

		$workerId = $this->registry->get('workerId');

		$path = DATA_PATH . $batchId;
		if (!file_exists($path))
		{
			mkdir($path, $this->getConfig('dirPermissions'));
			chmod($path, $this->getConfig('dirPermissions'));
		}

		$path .= '/' . $workerId;
		if (!file_exists($path))
		{
			mkdir($path, $this->getConfig('dirPermissions'));
			chmod($path, $this->getConfig('dirPermissions'));
		}

		$path .= '/';

		return $path;
	}
}