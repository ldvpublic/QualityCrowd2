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
			<span id="step">Step <?= ($stepId+1) ?> of <?= $stepCount ?></span>

			<?php if($state == 'edit'): ?>
			<span class="debugmessage">PREVIEW MODE, all data will be deleted</span>
			<?php endif; ?>

			<?php if($isLocked && $stepId < $stepCount - 1): ?>
			<span id="timeout">Remaining time to finish this step: <?= formatTime($timeout) ?></span>
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

		<div class="footer">
			<?php if ($stepId + 1 < $stepCount && $state <> 'post' && $isLocked): ?>
				<button id="button_next">Next</button>
			<?php endif; ?>

			<?php if($state == 'post'): ?>
			<span class="debugmessage">
				This job is already completed, no more work for you. Sorry.
			</span>
			<?php endif; ?>

			<?php if(!$isLocked && $stepId < $stepCount - 1): ?>
			<span class="debugmessage">
				There are currently working enough people on this job. Try again later.
			</span>
			<?php endif; ?>
		</div>

		<script type="text/javascript">
			<?php if ($stepId + 1 < $stepCount && $state <> 'post' && $isLocked): ?>
			$('#button_next').click(function() {$('#stepform').trigger('submit');});
			<?php endif; ?>

			<?php if($isLocked && $stepId < $stepCount - 1): ?>
			var remainingTime = <?= $timeout ?>;
			var timer = window.setInterval(function () 
			{	
				if (remainingTime > 0) {
					remainingTime--;
					$('#timeout').html('Remaining time to finish this step: ' + formatTime(remainingTime));
				} else {
					window.clearInterval(timer);
				}
			}, 1000);

			function formatTime(seconds)
			{
			    var hours = Math.floor(seconds / 3600);
			    seconds = seconds - (hours * 3600);
			    var minutes = Math.floor(seconds / 60);
			    seconds = Math.round(seconds - (minutes * 60), 0);
			    
			    if(hours < 10) hours = "0" + hours;
				if(minutes < 10) minutes = "0" + minutes;
				if(seconds < 10) seconds = "0" + seconds;

			    return hours + ':' + minutes + ':' + seconds;
			}

			<?php endif; ?>
		</script>
	</body>
</html>