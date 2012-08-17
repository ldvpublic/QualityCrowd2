<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>QualityCrowd 2</title>

		<link rel="stylesheet" href="<?= BASE_URL ?>core/files/css/style.css" />

		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/swfobject.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/flashver.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/jquery.js"></script>
	</head>
	<body>
		<div class="header">
			Step <?= ($stepId+1) ?> of <?= $stepCount ?>
			<?php if($state == 'edit'): ?>
				<span class="debugmessage">PREVIEW MODE, all data will be deleted</span>
			<?php endif; ?>
		</div>

		<h1><?= $title ?></h1>

		<?php if (isset($msg) && is_array($msg)):?>
		<ul class="errormessage">
			<?php foreach($msg as $m): ?>
			<li><?= $m ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<form action="<?= BASE_URL.$batchId.'/'.$workerId ?>" method="post" id="stepform">
			<input type="hidden" name="stepId" value="<?= $stepId ?>">

			<?= $content ?>
		</form>

		<?php if($state == 'post'): ?>
			<span class="debugmessage">
				This test is already completed, no more work for you. Sorry.
			</span>
		<?php endif; ?>

		<div class="footer">
			<?php if ($stepId + 1 <> $stepCount && $state <> 'post'): ?>
			<button id="button_next">Next</button>
			<?php endif; ?>
		</div>

		<?php if ($stepId + 1 <> $stepCount): ?>
		<script type="text/javascript">
			$('#button_next').click(function() {$('#stepform').trigger('submit');});
		</script>
		<?php endif; ?>
	</body>
</html>