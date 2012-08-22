<p><?= $text ?></p>

<?php foreach($videos as $k => $v): ?>
	<?= $v ?>
<?php endforeach; ?>

<input type="hidden" name="watched" value="0">

<script type="text/javascript">
	var videosWatched = new Object();
	<?php foreach($videos as $k => $v): ?>
	videosWatched['<?= $k ?>'] = false;
	<?php endforeach; ?>

	function onVideoComplete(videoid) 
	{
		videosWatched[videoid] = true;

		var allWatched = true;
		for (id in videosWatched) {
			if (!videosWatched[id]) allWatched = false;
		}

		if (allWatched) {
			$('input[name=watched]').val(1);
		}
	}
</script>

<p><?= $question ?></p>

<?= $answerform ?>
