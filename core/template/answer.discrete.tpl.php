<input type="hidden" name="answered" value="0">

<?php
$answers = array_reverse($answers);
foreach($answers as $row): ?>
	<input type="radio" name="value" id="value-<?= $row['value'] ?>" value="<?= $row['value'] ?>">
	<label for="value-<?= $row['value'] ?>" id="label-<?= $row['value'] ?>"><?= $row['text'] ?></label>
	<br />
<?php endforeach; ?>

<input type="hidden" name="text" value="">
<input type="hidden" name="answermode" value="discrete">

<script type="text/javascript">

	$('input[name=value]').change( function() {
		var selectedValue = $('input[name=value]:checked').val();
		$('input[name=text]').val($('#label-' + selectedValue).text());
		$('input[name=answered]').val(1);
	});

</script>

