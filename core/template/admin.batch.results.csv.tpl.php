<?php
header('Content-type: text/csv');
header('Content-disposition: attachment;filename=' . $batchId . '.csv');

// Header line 1
echo 'Worker ID,Finished';
foreach($steps as $stepId => $step)
{
	echo ',Step ' . ($stepId + 1);
}
echo "\n";

// Header line 2
echo ',';
foreach($steps as $step)
{
	echo ',' . ifset($step['arguments']['name']);
}
echo "\n";


// Results
foreach($workers as $worker)
{
	echo $worker['workerId'] . ',';
	echo ($worker['finished'] ? 'Yes' : 'No');

	if (is_array($worker['results'])) {
		foreach($worker['results'] as $result)
		{
			if (isset($result[3])) {
				echo ',' . $result[3];
			} else {
				echo ',-';
			}
		}
	}

	echo "\n";
}

?>