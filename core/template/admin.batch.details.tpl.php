<h3>Info</h3>
<table class="meta">
<?php foreach($properties as $k => $v): ?>
	<tr>
		<th><?= ucfirst($k) ?></th>
		<td><?= ($k == 'timeout' ? formatTime($v) : $v) ?></td>
	</tr>
<?php endforeach; ?>
	<tr>
		<th>State</th>
		<td><?= Batch::readableState($state) ?></td>
	</tr>
	<tr>
		<th>Worker URL</th>
		<td><?= (BASE_URL . $id . '/&lt;worker id&gt;') ?></td>
	</tr>
</table>

<h3>Steps</h3>
<table class="steps">
<?php foreach($steps as $sk => $step):
	$o = '';
	$rows = count($step['properties']) + 1;
	foreach($step['elements'] as $ek => $e) {
		$rows += 1;
		$rows += count($e['properties']);

		$o .= '<tr class="element">';
		$o .= '<td class="command">' . $e['command'] . '</td>';
		$o .= '<td>' . trimText(implode(' &nbsp; &nbsp; ', $e['arguments']), 70) . '</td>';
		$o .= '</tr>';

		if (isset($e['properties'])) {
			ksort($e['properties']);
			foreach($e['properties'] as $pk => $pv) {
				$o .= '<tr class="property">';
				$o .= '<td class="property-key">' . $pk . '</td>';
				$o .= '<td class="property-value">'. formatPropertyValue($pv) . '</td>';
				$o .= '</tr>';
			}
		}
	}
	?>
	<tr class="step">
		<td class="number" rowspan="<?= $rows ?>"><?= ($sk + 1) ?></td>
		<td class="preview" colspan="2">
			<a href="<?= BASE_URL ?>admin/batch/<?= $id ?>/<?= $sk ?>">Preview</a>
		</td>
	</tr>
	<?php if (isset($step['properties'])):
		ksort($step['properties']);
		foreach($step['properties'] as $pk => $pv): ?>
			<tr class="property">
				<td class="property-key"><?= $pk ?></td>
				<td class="property-value"><?= formatPropertyValue($pv) ?></td>
			</tr>
		<?php endforeach; 
	endif;?>
	<?= $o ?>
<?php endforeach; ?>
</table>

