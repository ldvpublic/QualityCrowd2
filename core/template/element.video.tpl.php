<?php foreach($videos as $k => $v): ?>
	<?= $v ?>
<?php endforeach; ?>

<input type="hidden" name="watched-<?= $uid ?>" value="0">

<script type="text/javascript">
	var videosWatched_<?= $uid ?> = new Object();
	<?php foreach($videos as $k => $v): ?>
	videosWatched_<?= $uid ?>['<?= $k ?>'] = false;
	<?php endforeach; ?>

	function onVideoComplete(videoid) 
	{
		videosWatched_<?= $uid ?>[videoid] = true;

		var allWatched = true;
		for (id in videosWatched_<?= $uid ?>) {
			if (!videosWatched_<?= $uid ?>[id]) allWatched = false;
		}

		if (allWatched) {
			$('input[name=watched-<?= $uid ?>]').val(1);
		}
	}
</script>
