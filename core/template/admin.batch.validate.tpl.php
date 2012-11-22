<form action="?" method="get">
	<fieldset>
		<legend>Search</legend>
		<label for="workerid">Worker ID</label>
		<input name="workerid">
	</fieldset>

	<button>Search</button>
</form>

<?php if ($query <> ''): ?>
<h3>Result</h3>
<table class="meta">
	<?php if (is_array($result)): ?>
	<tr>
		<th>Worker ID</th>
		<td><?= $result['workerId'] ?></td>
	</tr>
	<tr>
		<th>Started at</th>
		<td><?= date('d.m.Y, H:i:s', $result['timestamp']) ?></td>
	</tr>
	<tr>
		<th>Last step</th>
		<td><?= ($result['stepId'] + 1) ?></td>
	</tr>
	<tr>
		<th>Finished</th>
		<td style="background-color:<?= ($result['finished'] ? '#00ff00' : '#ff0000') ?>">
			<?= ($result['finished'] ? 'Yes' : 'No') ?>
		</td>
	</tr>
<?php else: ?>
	<tr>
		<th>Query</th>
		<td><?= $query ?></td>
	</tr>
	<tr>
		<th>Result</th>
		<td>not found</td>
	</tr>
<?php endif; ?>
</table>
<?php endif; ?>