<form action="<?= BASE_URL.$batchId.'/'.$workerId ?>" method="post" id="stepform">
	<input type="hidden" name="stepId-<?= $scope ?>" value="<?= $stepId ?>">

	<?php foreach($elements as $e):?>
		<?= $e ?>
	<?php endforeach; ?>
</form>