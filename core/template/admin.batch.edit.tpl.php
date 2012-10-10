<form action="<?= BASE_URL ?>admin/batch/<?= $id ?>/edit" method="post" id="qcsform">
	<fieldset>
		<legend>Settings</legend>
		<label for="state">State</label>
		<select name="state">
			<?php if ($state == 'edit'): ?>
			<option value="edit" <?= ($state == 'edit' ? 'selected="selected"' : '') ?>>
				<?= Batch::readableState('edit') ?>
			</option>
			<?php endif; ?>
			<option value="active" <?= ($state == 'active' ? 'selected="selected"' : '') ?>>
				<?= Batch::readableState('active') ?>
			</option>
			<?php if ($state == 'active' || $state == 'post'): ?>
			<option value="post" <?= ($state == 'post' ? 'selected="selected"' : '') ?>>
				<?= Batch::readableState('post') ?>
			</option>
			<?php endif; ?>
		</select>
		<?php if ($state == 'edit'): ?>
		<p>Changing from "<?= Batch::readableState('edit') ?>" to "<?= Batch::readableState('active') ?>" deletes all result data!</p>
		<?php endif; ?>
	</fieldset>

	<?php if ($state <> 'edit'): ?>
	<button id="button_save">Save</button>
	<?php endif; ?>

	<fieldset>
		<legend>QC-Script<?php if ($state <> 'edit'): ?> (read only)<?php endif; ?></legend>
		<textarea id="code" name="qcs"><?= $qcs ?></textarea>
	</fieldset>
	
	<?php if ($state == 'edit'): ?>
	<button id="button_save">Save</button>
	<?php endif; ?>
</form>

<script>
	var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
		lineNumbers: true,
		theme: 'ambiance',
		lineWrapping: true,
		<?php if ($state <> 'edit'): ?>
		readOnly: true,
		<?php endif; ?>
	});
</script>
