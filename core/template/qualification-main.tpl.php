<div class="header header-qualification">
	<span class="step">Qualification: step <?= ($stepId + 1) ?> of <?= $stepCount ?></span>

	<?php if($state == 'edit'): ?>
	<span class="debugmessage">PREVIEW MODE, all data will be deleted</span>
	<?php endif; ?>
</div>

<input type="hidden" name="stepId-<?= $scope ?>" value="<?= $stepId ?>">

<?php if (isset($msg) && is_array($msg)):?>
	<ul class="errormessage">
		<?php foreach($msg as $m): ?>
		<li><?= $m ?></li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?= $content ?>
