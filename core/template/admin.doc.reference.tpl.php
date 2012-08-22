<?php foreach($syntax as $cmd => $cmdDef): ?>
	<h2><?= $cmd ?></h2>
	<h3>Usage</h3>

	<?php 
	$argStr = '';
	$i = 1;
	foreach ($cmdDef['arguments'] as $arg) {
		if ($i > $cmdDef['minArguments']) {
			$argStr .= ' [&lt;' . $arg .'&gt;]';
		} else {
			$argStr .= ' &lt;' . $arg .'&gt;';
		}
		$i++;
	}
	?>

	<pre><?= $cmd ?><?= $argStr ?></pre>

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
