<input type="hidden" name="answered-<?= $uid ?>" value="0">
<input type="hidden" name="text-<?= $uid ?>" value="">

<?php foreach($answers as $row): ?>
	<input type="radio" name="value-<?= $uid ?>" id="value-<?= $row['value'] ?>-<?= $uid ?>" value="<?= $row['value'] ?>">
	<label for="value-<?= $row['value'] ?>" id="label-<?= $row['value'] ?>-<?= $uid ?>"><?= $row['text'] ?></label>
	<br />
<?php endforeach; ?>


<script type="text/javascript">

	$('input[name=value-<?= $uid ?>]').change( function() {
		var selectedValue = $('input[name=value-<?= $uid ?>]:checked').val();
		$('input[name=text-<?= $uid ?>]').val($('#label-' + selectedValue + '-<?= $uid ?>').text());
		$('input[name=answered-<?= $uid ?>]').val(1);
	});

</script>

