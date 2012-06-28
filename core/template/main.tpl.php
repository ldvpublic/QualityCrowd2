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
			<?php /*<a href="<?= BASE_URL ?><?= $batchId ?>/<?= $workerId ?>?restart=">Restart</a> */ ?>
		</div>

		<h1><?= $title ?></h1>

		<?php if (is_array($msg)):?>
		<ul class="errormessage">
			<?php foreach($msg as $m): ?>
			<li><?= $m ?></li>
			<?php endforeach; ?>
		</ul>
		<?php endif; ?>

		<form action="<?= BASE_URL ?><?= $batchId ?>/<?= $workerId ?>" method="post" id="stepform">
			<input type="hidden" name="stepId" value="<?= $stepId ?>">

			<?= $content ?>
		</form>

		<div class="footer">
			<?php if ($stepId + 1 <> $stepCount): ?>
			<button id="button_next">Next</button>
			<?php endif; ?>
			<?php if ($stepId == -1): ?>
			<button id="button_restart">Restart</button>
			<?php endif; ?>
		</div>

		<script type="text/javascript">
			<?php if ($stepId + 1 <> $stepCount): ?>
			$('#button_next').click(function() {$('#stepform').trigger('submit');});
			<?php endif; ?>
			<?php if ($stepId == -1): ?>
			$('#button_restart').click(function() {
				window.location.href = window.location.href + '?restart='; 
			});
			<?php endif; ?>
		</script>
	</body>
</html>