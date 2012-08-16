<?php
require_once (ROOT_PATH .'core'.DS.'3p'.DS.'jpgraph'.DS.'src'.DS.'jpgraph.php');
require_once (ROOT_PATH .'core'.DS.'3p'.DS.'jpgraph'.DS.'src'.DS.'jpgraph_bar.php');

// prepare workers graph
$dataY = array();
$labelsX = array();
foreach($steps as $stepId => &$step)
{
	$dataY[] = $step['workers'];
	$labelsX[] = ($stepId + 1);
}

// setup the graph
$graph = new Graph(700,300);
$graph->SetScale("textlin");

$theme_class = new UniversalTheme;

$graph->SetTheme($theme_class);
$graph->img->SetAntiAliasing(true);
$graph->SetBox(false);
$graph->SetMargin(50,5,50,45);

$graph->title->SetFont(FF_FONT2,FS_BOLD,12);
$graph->title->Set("Workers per Step");
$graph->subtitle->SetFont(FF_FONT1,FS_NORMAL,8);
$graph->subtitle->Set("Batch: " . $id);

$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$graph->yaxis->SetTitle("Workers", 'center');
$graph->yaxis->SetTitleMargin(40);

$graph->xgrid->Show();
$graph->xgrid->SetLineStyle("solid");
$graph->xaxis->SetTickLabels($labelsX);
$graph->xaxis->SetTitle("Step", 'center');
$graph->xgrid->SetColor('#E3E3E3');

// plot line
$p1 = new BarPlot($dataY);
$graph->Add($p1);
$p1->SetColor("olivedrab3");
$p1->SetFillGradient('olivedrab1','olivedrab4',GRAD_VERT);

// output graph to temp file
$graph->Stroke(TMP_PATH.'img-cache'.DS.'workers-'.$id.'.png');

?>

<h3>Workers</h3>

<img src="<?= BASE_URL.'core/tmp/img-cache/workers-'.$id.'.png' ?>">

<h3>Consolidated Results</h3>
<table class="steps">
<?php foreach($steps as $stepId => &$step): ?>
	<tr class="step">
		<td class="number" rowspan="8"><?= ($stepId + 1) ?></td>
		<td class="command" colspan="2"><?= $step['command'] ?></td>
		<td></td>
	</tr>
	<tr class="property">
		<td class="property-key" colspan="2">workers</td>
		<td class="property-value"><?= $step['workers'] ?></td>
	</tr>
	<tr class="property">
		<td class="property-key" rowspan="3">duration</td>
		<td class="property-key">average</td>
		<td class="property-value"><?= date('i:s', $step['duration-avg']) ?></td>
	</tr>
	<tr class="property">
		<td class="property-key">maximum</td>
		<td class="property-value"><?= date('i:s', $step['duration-max']) ?></td>
	</tr>
	<tr class="property">
		<td class="property-key">minimum</td>
		<td class="property-value"><?= date('i:s', $step['duration-min']) ?></td>
	</tr>
	<tr class="property">
		<td class="property-key" rowspan="3">result</td>
		<td class="property-key">average</td>
		<td class="property-value"><?= round($step['results-avg'],1) ?></td>
	</tr>
	<tr class="property">
		<td class="property-key">maximum</td>
		<td class="property-value"><?= $step['results-max'] ?></td>
	</tr>
	<tr class="property">
		<td class="property-key">minimum</td>
		<td class="property-value"><?= $step['results-min'] ?></td>
	</tr>
<?php endforeach; ?>
</table>

