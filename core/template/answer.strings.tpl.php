<input type="hidden" name="answered" value="0">
<input type="hidden" name="answermode" value="strings">

<fieldset>
	<legend><?= $question ?></legend>

	<table>
	<?php foreach($answers as $row): ?>
		<tr>
			<td>
				<label for="value-<?= $row['value'] ?>" id="label-<?= $row['value'] ?>"><?= $row['text'] ?></label>
			</td>
			<td>
				<input type="text" name="value-<?= $row['value'] ?>" id="value-<?= $row['value'] ?>" value="">
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
</fieldset>

<script type="text/javascript">

	$('input[type=text]').keyup( function() {
		var answered = 1;
		$('input[type=text]').each(function(i, item) {
			if($(item).val().length == 0) {
				answered = 0;
			}
		});
		
		$('input[name=answered]').val(answered);
	});

</script>

