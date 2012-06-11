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
		<div class="stepprogress">
			Step <?= ($stepId+1) ?> of <?= $stepCount ?>
			<a href="<?= BASE_URL ?><?= $batchId ?>/<?= $workerId ?>?restart=">Restart</a>
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

		<?php if ($stepId + 1 <> $stepCount): ?>
		<div class="stepbuttons">
			<button id="button_next">Next</button>
		</div>
		<?php endif; ?>

		<script type="text/javascript">
			$('#button_next').click(function() {$('#stepform').trigger('submit');});
		</script>
	</body>
</html>