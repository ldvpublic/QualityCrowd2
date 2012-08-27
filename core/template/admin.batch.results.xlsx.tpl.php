<?php
spl_autoload_unregister('myAutoloader');
require_once ROOT_PATH.'core'.DS.'3p'.DS.'phpexcel'.DS.'Classes'.DS.'PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("QualityCrowd 2")
							 ->setTitle("$batchId - results")
							 ->setDescription("QualityCrowd 2 results for $batchId");

// fill in results
outputHeaders($objPHPExcel->getSheet(0), $steps);
outputResults($objPHPExcel->getSheet(0), $workers, 3);
$objPHPExcel->getSheet(0)->setTitle('results');

// fill in text results
$objPHPExcel->createSheet();
outputHeaders($objPHPExcel->getSheet(1), $steps);
outputResults($objPHPExcel->getSheet(1), $workers, 4);
$objPHPExcel->getSheet(1)->setTitle('text results');

// fill in durations
$objPHPExcel->createSheet();
outputHeaders($objPHPExcel->getSheet(2), $steps);
outputDurations($objPHPExcel->getSheet(2), $workers);
$objPHPExcel->getSheet(2)->setTitle('durations');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// redirect output to client browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $batchId . '.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');



function outputHeaders($sheet, $steps)
{
	// Header line 1
	$sheet->setCellValue('A1', 'Worker ID')
        ->setCellValue('B1', 'Token')
        ->setCellValue('C1', 'Finished');

	$c = 3;
	foreach($steps as $stepId => $step)
	{
	    $sheet->setCellValueByColumnAndRow($c, 1, 'Step ' . ($stepId + 1));
	    $c++;
	}

	// Header line 2
	$c = 3;
	foreach($steps as $step)
	{
		$sheet->setCellValueByColumnAndRow($c, 2, $step['command']);
	    $c++;
	}

	// Header line 3
	$c = 3;
	foreach($steps as $step)
	{
		if (isset($step['arguments'][0]) && $step['command'] <> 'page') {
			$sheet->setCellValueByColumnAndRow($c, 3, $step['arguments'][0]);
		}
		$c++;
	}

}

function outputResults($sheet, $workers, $colId)
{
	$r = 4;
	foreach($workers as $worker)
	{
		$sheet->setCellValueExplicitByColumnAndRow(0, $r, $worker['workerId'],
			PHPExcel_Cell_DataType::TYPE_STRING);
	    $sheet->setCellValueExplicitByColumnAndRow(1, $r, $worker['token'], 
	    	PHPExcel_Cell_DataType::TYPE_STRING);
	    $sheet->setCellValueByColumnAndRow(2, $r, ($worker['finished'] ? 'Yes' : 'No'));

		if (is_array($worker['results'])) {
			$c = 3;
			foreach($worker['results'] as $result)
			{
				if (isset($result[$colId])) {
					$sheet->setCellValueByColumnAndRow($c, $r, $result[$colId]);
				} else {
					$sheet->setCellValueByColumnAndRow($c, $r, '-');
				}
				$c++;
			}
		}

		$r++;
	}
}

function outputDurations($sheet, $workers)
{
	$r = 4;
	foreach($workers as $worker)
	{
		$sheet->setCellValueExplicitByColumnAndRow(0, $r, $worker['workerId'],
			PHPExcel_Cell_DataType::TYPE_STRING);
	    $sheet->setCellValueExplicitByColumnAndRow(1, $r, $worker['token'], 
	    	PHPExcel_Cell_DataType::TYPE_STRING);
	    $sheet->setCellValueByColumnAndRow(2, $r, ($worker['finished'] ? 'Yes' : 'No'));

		if (is_array($worker['durations'])) {
			$c = 3;
			foreach($worker['durations'] as $stepId => $duration)
			{
				$sheet->setCellValueByColumnAndRow($c, $r, $duration);
				$c++;
			}
		}

		$r++;
	}
}


