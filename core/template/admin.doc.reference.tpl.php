<?php foreach($syntax as $cmd => $cmdDef): ?>
	<h2><?= $cmd ?></h2>
	<h3>Usage</h3>

	<?php 
	$cmdStr = $cmd;
	$i = 1;
	foreach ($cmdDef['arguments'] as $arg) {
		if ($i > $cmdDef['minArguments']) {
			$cmdStr .= ' [&lt;' . $arg .'&gt;]';
		} else {
			$cmdStr .= ' &lt;' . $arg .'&gt;';
		}
		$i++;
	}
	if ($cmdDef['isBlock']) {
		$cmdStr .= "\n   ...\nend " . $cmd;
	}
	?>

	<pre><?= $cmdStr ?></pre>

	<?= Markdown($cmdDef['description']) ?>

	<?php if(isset($cmdDef['properties']) && count($cmdDef['properties']) > 0): ?>
	<h3>Properties</h3>
	<table class="meta">
		<tr>
			<th>Property</th>
			<th>Default value</th>
		</tr>
		<?php foreach($cmdDef['properties'] as $property => $default): ?>
		<tr>
			<td><?= $property ?></td>
			<td><?= formatPropertyValue($default) ?></td>
		</tr>
		<?php endforeach; ?>
	<table>
	<?php endif; ?>
<?php endforeach; ?>
