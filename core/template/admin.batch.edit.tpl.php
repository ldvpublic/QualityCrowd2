<form action="<?= BASE_URL ?>admin/batch/<?= $id ?>/edit" method="post" id="qcsform">
	<textarea id="code" name="qcs"><?= $qcs ?></textarea>
	<button id="button_save">Save</button>
</form>

<script>
	var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
		lineNumbers: true,
		theme: 'ambiance'
	});

	$('#button_save').click(function() {$('#qcsform').trigger('submit');});
</script>
