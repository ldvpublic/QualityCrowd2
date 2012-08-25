<?php

require(ROOT_PATH.'core'.DS.'lib'.DS.'fstools.php');

class DataStore extends Base
{
	public function writeWorker($key, $data, $batchId, $workerId)
	{
		$path = $this->getDataPath('worker', $batchId, $workerId, true);
		$this->write('json', $path, $key, $data);
	}

	public function writeWorkerCSV($key, $data, $batchId, $workerId)
	{
		$path = $this->getDataPath('worker', $batchId, $workerId, true);
		$this->write('csv', $path, $key, $data);
	}

	public function writeBatch($key, $data, $batchId)
	{
		$path = $this->getDataPath('meta', $batchId, '', true);
		$this->write('json', $path, $key, $data);
	}

	public function readWorker($key, $default = null, $batchId, $workerId)
	{
		$path = $this->getDataPath('worker', $batchId, $workerId);
		return $this->read('json', $path, $key, $default);
	}

	public function readWorkerCSV($key, $batchId, $workerId)
	{
		$path = $this->getDataPath('worker', $batchId, $workerId);
		return $this->read('csv', $path, $key, null);
	}

	public function readBatch($key, $default = null, $batchId)
	{
		$path = $this->getDataPath('meta', $batchId);
		return $this->read('json', $path, $key, $default);
	}

	public function deleteWorker($batchId, $workerId, $key = '')
	{
		$path = $this->getDataPath('worker', $batchId, $workerId);
		$this->delete('json', $path, $key);
	}

	public function deleteAllWorkers($batchId)
	{
		$path = $this->getDataPath('batch', $batchId);
		rrmdir($path . 'workers' . DS);
	}

	private function read($type, $path, $key, $default)
	{
		$file = $path . $key . $this->getExtensionFromType($type);

		switch($type)
		{
			case 'json':
				if (!file_exists($file)) return $default;
				$data = file_get_contents($file);
				$data = unserialize($data);
				return $data;

			case 'csv':
				if (!file_exists($file)) return null;

				$rows = array();
				$fh = fopen($file, 'r');
				while($row = fgetcsv($fh)) {
					$rows[] = $row;
				}
				fclose($fh);

				return $rows;

			default:
				throw new Exception("Unknown data type '$type'");
				break;
		}
	}

	private function write($type, $path, $key, $data)
	{
		$file = $path . $key . $this->getExtensionFromType($type);

		switch($type)
		{
			case 'json':
				$data = serialize($data);
				file_put_contents($file, $data);
				break;

			case 'csv':
				$fh = fopen($file, 'a');
				foreach($data as $row) {
					fputcsv($fh, $row);
				}
				fclose($fh);
				break;

			default:
				throw new Exception("Unknown data type '$type'");
				break;
		}

		chmod($file, $this->getConfig('filePermissions'));
	}

	private function delete($type, $path, $key)
	{
		if ($key == '')
		{
		    $files = glob($path . '*', GLOB_MARK);
		    foreach ($files as $file) 
		    {
		        unlink($file);
		    }
		    rmdir($path);
		} else
		{
			$file = $path . $key . $this->getExtensionFromType($type);
			echo $file;
			if (file_exists($file))
			{
				unlink($file);
			}
		}
	}


	/* possible scopes:
	 *  - batch    /data/<batchid>/
	 *  - meta     /data/<batchid>/meta/<key> 
	 *  - workers  /data/<batchid>/workers/<workerid>/<key> 
	 */

	private function getDataPath($scope, $batchId, $workerId = '', $create = false) 
	{
		$path = '';
		switch ($scope)
		{
			case 'batch':
				$path = DATA_PATH . $batchId;
				if ($create) $this->createDir($path);
				break;

			case 'meta':
				$path = $this->getDataPath('batch', $batchId, '', $create);

				$path = $path . 'meta';
				if ($create) $this->createDir($path);
				break;

			case 'worker':
				$path = $this->getDataPath('batch', $batchId, '', $create);

				$path = $path . 'workers';
				if ($create) $this->createDir($path);

				$path = $path . DS . $workerId;
				if ($create) $this->createDir($path);

				break;
		}

		$path .= DS;
		return $path;
	}

	private function createDir($path)
	{
		if (!file_exists($path))
		{
			mkdir($path, $this->getConfig('dirPermissions'));
			chmod($path, $this->getConfig('dirPermissions'));
		}
	}

	private function getExtensionFromType($type) 
	{
		switch($type)
		{
			case 'json': return '.txt';
			case 'csv':  return '.csv';
		}
	}
}