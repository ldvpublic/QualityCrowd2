<h3>Workers</h3>

<table class="">
	<tr>
		<th>Worker ID</th>
		<th>Token</th>
		<th>Timestamp</th>
	</tr>
<?php foreach($workers as $w): ?>
	<tr>
		<td><?= $w['workerId'] ?></td>
		<td><?= $w['token'] ?></td>
		<td><?= $w['timestamp'] ?></td>
		<td><?= $w['useragent'] ?></td>
	</tr>
<?php endforeach; ?>
</table>

<?php 

$steps = array();

foreach($workers as $wid => $w)
{
	foreach($w['results'] as $stepId => $stepResults)
	{
		$steps[$stepId]['command'] = $stepResults[1];
		array_shift($stepResults);
		array_shift($stepResults);
		array_shift($stepResults);
		$steps[$stepId]['results'][$wid] = $stepResults;
	}
}	

?>

<h3>Results</h3>
<table class="steps">
<?php foreach($steps as $stepId => $step): ?>
	<tr class="step">
		<td class="number" rowspan="<?= count($step['results']) + 1 ?>"><?= ($stepId + 1) ?></td>
		<td class="command"><?= $step['command'] ?></td>
		<td colspan="5"></td>
	</tr>
	<?php 
	foreach($step['results'] as $wid => $r): ?>
	<tr class="property">
		<td class="property-key"><?= $wid ?></td>
		<td class="property-value" colspan="2"><?= implode('<td>', $r) ?></td>
	</tr>
	<?php endforeach; ?>
<?php endforeach; ?>
</table>

