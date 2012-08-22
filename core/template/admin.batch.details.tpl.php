<h3>Info</h3>
<table class="meta">
<?php foreach($properties as $k => $v): ?>
	<tr>
		<th><?= ucfirst($k) ?></th>
		<td><?= $v ?></td>
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
<?php foreach($steps as $k => $v): ?>
	<tr class="step">
		<td class="number" rowspan="<?= (count($v['properties']) + 1) ?>"><?= ($k + 1) ?></td>
		<td class="command"><?= $v['command'] ?></td>
		<td class="argument"><?= trimText(implode(' &nbsp; &nbsp; ', $v['arguments']), 70) ?></td>
		<td class="argument"><a href="<?= BASE_URL ?>admin/batch/<?= $id ?>/<?= $k ?>">Preview</a></td>
	</tr>
	<?php 
	ksort($v['properties']);
	foreach($v['properties'] as $pk => $pv): ?>
	<tr class="property">
		<td class="property-key"><?= $pk ?></td>
		<td class="property-value" colspan="2"><?= formatPropertyValue($pv) ?></td>
	</tr>
	<?php endforeach; ?>
<?php endforeach; ?>
</table>

