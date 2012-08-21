<?php
spl_autoload_unregister('myAutoloader');
require_once ROOT_PATH.'core'.DS.'3p'.DS.'phpexcel'.DS.'Classes'.DS.'PHPExcel.php';

// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("QualityCrowd 2")
							 ->setLastModifiedBy("QualityCrowd 2")
							 ->setTitle("$batchId - results")
							 ->setSubject("QualityCrowd 2 results")
							 ->setDescription("QualityCrowd 2 results for $batchId");

// Header line 1
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'Worker ID')
            ->setCellValue('B1', 'Token')
            ->setCellValue('C1', 'Finished');

$c = 3;
foreach($steps as $stepId => $step)
{
    $objPHPExcel->getActiveSheet(0)
    	->setCellValueByColumnAndRow($c, 1, 'Step ' . ($stepId + 1));
    $c++;
}

// Header line 2
$c = 3;
foreach($steps as $step)
{
	$objPHPExcel->getActiveSheet(0)
    	->setCellValueByColumnAndRow($c, 2, $step['command']);
    $c++;
}

// Header line 3
$c = 3;
foreach($steps as $step)
{
	if (isset($step['arguments'][0]) && $step['command'] <> 'page') {
		$objPHPExcel->getActiveSheet(0)
    		->setCellValueByColumnAndRow($c, 3, $step['arguments'][0]);
	}
	$c++;
}

// Results
$r = 4;
foreach($workers as $worker)
{
	$objPHPExcel->getActiveSheet(0)
    	->setCellValueExplicitByColumnAndRow(0, $r, $worker['workerId'], PHPExcel_Cell_DataType::TYPE_STRING)
    	->setCellValueExplicitByColumnAndRow(1, $r, $worker['token'], PHPExcel_Cell_DataType::TYPE_STRING)
    	->setCellValueByColumnAndRow(2, $r, ($worker['finished'] ? 'Yes' : 'No'));

	if (is_array($worker['results'])) {
		$c = 3;
		foreach($worker['results'] as $result)
		{
			if (isset($result[3])) {
				$objPHPExcel->getActiveSheet(0)
    				->setCellValueByColumnAndRow($c, $r, $result[3]);
			} else {
				$objPHPExcel->getActiveSheet(0)
    				->setCellValueByColumnAndRow($c, $r, '-');
			}
			$c++;
		}
	}

	$r++;
}

// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle($batchId);

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// redirect output to client browser
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $batchId . '.xlsx"');
header('Cache-Control: max-age=0');

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output'); 
