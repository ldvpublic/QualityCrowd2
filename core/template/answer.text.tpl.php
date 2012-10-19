<input type="hidden" name="answered" value="0">
<input type="hidden" name="value" value="">
<input type="hidden" name="answermode" value="text">

<fieldset>
	<legend><?= $question ?></legend>
	<textarea name="text"></textarea>
</fieldset>

<script type="text/javascript">

	$('textarea[name=text]').keyup( function() {
		var text = $('textarea[name=text]').val();
		$('input[name=value]').val(text.length);
		$('input[name=answered]').val(text.length > 0);
	});

</script>

