<?php
$swf = BASE_URL . 'core/files/flash/qcplayer.swf';
$buttons = BASE_URL . 'core/files/img/playerbuttons.png';

$html5 = preg_match('/\.mp4$/i', $file) && preg_match('/Chrome/', $_SERVER['HTTP_USER_AGENT']);

?>

<div class="video-box" style="width:<?= $width ?>px; min-height:<?= ($height + 28) ?>px;">
	<div id="flash-fallback-<?= $uid ?>" class="flash-fallback">
		<p><b>You are using an version of Adobe's Flash Player plugin, that is not supported by this
		website.</b></p>
		<p>Click the button to download the latest version from Adobe:</p>
		<p><a href="http://www.adobe.com/go/getflashplayer">
			<img src="<?= BASE_URL ?>core/files/img/get_flash_player.gif" alt="Get Adobe Flash player" border="0" />
		</a></p>
	</div>
	<?php if($html5): ?>
	<video id="player-<?= $uid ?>" width="<?= $width ?>" height="<?= $height ?>" style="display:none" preload="auto">
		<source id="mysource-<?= $uid ?>" src="" type='video/mp4; codecs="avc1.64001E"' />
	</video>

	<div id="mycontrols-<?= $uid ?>" style="display:none;">
		<div id="playbutton-<?= $uid ?>" class="button playbutton"></div>
		<div id="progressbox-<?= $uid ?>" class="progressbox" style="width:<?= ($width - 52) ?>px;">
			<div id="progress-<?= $uid ?>" class="progress"></div>
			<div id="buffered-<?= $uid ?>" class="buffered"></div>
			<div id="buftext-<?= $uid ?>" class="buftext"></div>
		</div>
	</div>
	<?php endif; ?>
</div>


<script type="text/javascript">
	<?php if($html5): ?>
	
	player<?= $uid ?> = document.getElementById('player-<?= $uid ?>');
	
	var t;
	var isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

	if(!supportsVideo || !supportsH264() || !isChrome) {
		if (DetectFlashVer(10,0,0)) {
			embedFlashPlayer<?= $uid ?>();
		} else {
			$('#flash-fallback-<?= $uid ?>').show();
		}
	} else {
		$('#playbutton-<?= $uid ?>').click(playPause);
		$('#player-<?= $uid ?>').click(playPause);

		$('#player-<?= $uid ?>').show();
		$('#mycontrols-<?= $uid ?>').show();

		$('#playbutton-<?= $uid ?>').show();

		$('#mysource-<?= $uid ?>').attr('src', '<?= $file; ?>');
		startCount();
		$('#buftext-<?= $uid ?>').html('loading...');
		player<?= $uid ?>.load();
	}

	function playPause() {
		if ($('#playbutton-<?= $uid ?>').hasClass('playbutton')) {
			$('#playbutton-<?= $uid ?>').removeClass('playbutton');
			$('#playbutton-<?= $uid ?>').addClass('pausebutton');
			player<?= $uid ?>.play();
			
		} else {
			$('#playbutton-<?= $uid ?>').removeClass('pausebutton');
			$('#playbutton-<?= $uid ?>').addClass('playbutton');
			player<?= $uid ?>.pause();
		}
	}

	function startCount() {
		t = window.setInterval(function() {

			if (player<?= $uid ?>.duration) {
				$('#progress-<?= $uid ?>').width(player<?= $uid ?>.currentTime / player<?= $uid ?>.duration * <?= ($width - 52) ?>);
				$('#buffered-<?= $uid ?>').width(player<?= $uid ?>.buffered.end(0) / player<?= $uid ?>.duration * <?= ($width - 52) ?>);
			
				if (player<?= $uid ?>.buffered.end(0) >= player<?= $uid ?>.duration * 0.99) {
					$('#buftext-<?= $uid ?>').html('');
				} else {
					$('#buftext-<?= $uid ?>').html(Math.round(player<?= $uid ?>.buffered.end(0) / player<?= $uid ?>.duration * 100) + '%');
				}
			}

			if (player<?= $uid ?>.ended) {
				$('#playbutton-<?= $uid ?>').removeClass('pausebutton');
				$('#playbutton-<?= $uid ?>').addClass('playbutton');

				if (typeof(onVideoComplete) == 'function') {
					onVideoComplete('<?= $filename ?>');
				}
			}
		}, 100);
	}

	function supportsVideo() {
		return !!document.createElement('video').canPlayType;
	}

	function supportsH264() {
		var v = document.createElement("video");
		return v.canPlayType('video/mp4; codecs="avc1.64001E"');
	}
	<?php endif; ?>

	function embedFlashPlayer<?= $uid ?>() {
		var flashvars = {};
		flashvars.videoid = "<?= $filename; ?>";
		flashvars.video = "<?= $file; ?>";
		flashvars.width = <?= $width ?>;
		flashvars.height = <?= $height ?>;
		var params = {wmode:"transparent", scale: "noscale", align:"t", salign:"tl"};
		var attributes = {};
		swfobject.embedSWF("<?= $swf; ?>", "flash-fallback-<?= $uid ?>", "<?= $width ?>", "<?= ($height + 28 )?>;", "10.0.0", false, flashvars, params, attributes);
	}
	
	<?php if(!$html5): ?>
	embedFlashPlayer<?= $uid ?>();
	<?php endif; ?>
</script>