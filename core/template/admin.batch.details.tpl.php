<h3>Metadata</h3>
<table class="meta">
<?php foreach($properties as $k => $v): ?>
	<tr>
		<th><?= ucfirst($k) ?></th>
		<td><?= $v ?></td>
	</tr>
<?php endforeach; ?>
</table>

<h3>Steps</h3>
<table class="steps">
<?php foreach($steps as $k => $v): ?>
	<tr class="step">
		<td class="number" rowspan="<?= (count($v['properties']) + 1) ?>"><?= ($k + 1) ?></td>
		<td class="command"><?= $v['command'] ?></td>
		<td class="argument"><?= strip_tags(implode(' &nbsp; &nbsp; ', $v['arguments'])) ?></td>
		<td class="argument"><a href="<?= BASE_URL ?>admin/batch/<?= $id ?>/<?= $k ?>">Preview</a></td>
	</tr>
	<?php 
	ksort($v['properties']);
	foreach($v['properties'] as $pk => $pv): ?>
	<tr class="property">
		<td class="property-key"><?= $pk ?></td>
		<td class="property-value" colspan="2"><?= strip_tags($pv) ?></td>
	</tr>
	<?php endforeach; ?>
<?php endforeach; ?>
</table>

<h3>QC-Script</h3>
<pre class="codeblock"><?= $qcs ?></pre>
