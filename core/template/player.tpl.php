<?php
$uid = uniqid();

$swf = BASE_URL . 'core/files/flash/qcplayer.swf';
$buttons = BASE_URL . 'core/files/img/playerbuttons.png';

if (preg_match('/\.mp4$/i', $file)) {
	$html5 = true;
} else {
	$html5 = false;
}

?>

<input type="hidden" name="watched" value="0">

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
	<video id="myplayer-<?= $uid ?>" width="<?= $width ?>" height="<?= $height ?>" style="display:none">
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
	if (typeof(myPlayers) == 'undefined') {
		myPlayers = new Object;
	}
	
	myPlayers['<?= $uid ?>'] = document.getElementById('myplayer-<?= $uid ?>');
	
	var t;
	var allowPlaying = false;
	var isChrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

	if(!supportsH264() || !isChrome) {
		if (DetectFlashVer(10,0,0)) {
			embedFlashPlayer();
		} else {
			$('#flash-fallback-<?= $uid ?>').show();
		}
	} else {
		$('#playbutton-<?= $uid ?>').click(playPause);
		$('#myplayer-<?= $uid ?>').click(playPause);

		$('#myplayer-<?= $uid ?>').show();
		$('#mycontrols-<?= $uid ?>').show();

		$('#playbutton-<?= $uid ?>').hide();

		$('#mysource-<?= $uid ?>').attr('src', '<?= $file; ?>');
		startCount();
		$('#buftext-<?= $uid ?>').html('loading...');
		myPlayers['<?= $uid ?>'].load();
	}

	function playPause() {
		if ($('#playbutton-<?= $uid ?>').hasClass('playbutton')) {
			if (allowPlaying) {
				$('#playbutton-<?= $uid ?>').removeClass('playbutton');
				$('#playbutton-<?= $uid ?>').addClass('pausebutton');
				myPlayers['<?= $uid ?>'].play();
			}
		} else {
			$('#playbutton-<?= $uid ?>').removeClass('pausebutton');
			$('#playbutton-<?= $uid ?>').addClass('playbutton');
			myPlayers['<?= $uid ?>'].pause();
		}
	}

	function startCount() {
		t = window.setInterval(function() {

			if (myPlayers['<?= $uid ?>'].duration) {
				$('#progress-<?= $uid ?>').width(myPlayers['<?= $uid ?>'].currentTime / myPlayers['<?= $uid ?>'].duration * <?= ($width - 52) ?>);
				$('#buffered-<?= $uid ?>').width(myPlayers['<?= $uid ?>'].buffered.end(0) / myPlayers['<?= $uid ?>'].duration * <?= ($width - 52) ?>);
			

				if (myPlayers['<?= $uid ?>'].buffered.end(0) > myPlayers['<?= $uid ?>'].duration * 0.9) {
					$('#playbutton-<?= $uid ?>').show();
					allowPlaying = true;
				}
				if (myPlayers['<?= $uid ?>'].buffered.end(0) >= myPlayers['<?= $uid ?>'].duration * 0.99) {
					$('#buftext-<?= $uid ?>').html('');
				} else {
					$('#buftext-<?= $uid ?>').html('&nbsp;loading... ' + Math.round(myPlayers['<?= $uid ?>'].buffered.end(0) / myPlayers['<?= $uid ?>'].duration * 100) + '%');
				}
			}

			if (myPlayers['<?= $uid ?>'].ended) {
				$('#playbutton-<?= $uid ?>').removeClass('pausebutton');
				$('#playbutton-<?= $uid ?>').addClass('playbutton');

				if (typeof(onVideoComplete) == 'function') {
					onVideoComplete('<?= $id ?>');
				}
			}
		}, 100);
	}

	function supportsVideo() {
		return !!document.createElement('video').canPlayType;
	}

	function supportsH264() {
		if (!supportsVideo()) {
			return false;
		}
		var v = document.createElement("video");
		return v.canPlayType('video/mp4; codecs="avc1.64001E"');
	}
	<?php endif; ?>

	function embedFlashPlayer() {
		var flashvars = {};
			flashvars.videoid = "<?= $id; ?>";
			flashvars.video = "<?= $file; ?>";
			flashvars.width = <?= $width ?>;
			flashvars.height = <?= $height ?>;
			var params = {wmode:"transparent", scale: "noscale", align:"t", salign:"tl"};
			var attributes = {};
			swfobject.embedSWF("<?= $swf; ?>", "flash-fallback-<?= $uid ?>", "<?= $width ?>", "<?= ($height + 28 )?>;", "10.0.0", false, flashvars, params, attributes);
	}

	function onVideoComplete(videoid) {
		$('input[name=watched]').val(1);
	}
	
	<?php if(!$html5): ?>
	embedFlashPlayer();
	<?php endif; ?>
</script>