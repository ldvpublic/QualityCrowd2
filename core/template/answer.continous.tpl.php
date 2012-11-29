<input type="hidden" name="value-<?= $uid ?>" value="">
<input type="hidden" name="text-<?= $uid ?>" value="">
<input type="hidden" name="answered-<?= $uid ?>" value="0">

<?php

$maxVal = 0;
$fullHeight = 180;

foreach($answers as $row) {
	if ($row['value'] > $maxVal) $maxVal = $row['value'];
}

$showTicks = true;

$labels = '';
$delta = $fullHeight / $maxVal;
if (count($answers) > 2) {
	foreach($answers as $row) {
		if ($row['text'] <> '') {
			$pos = $fullHeight / $maxVal * ($maxVal - $row['value']) + $delta /2 - 5;
			$labels .= '<div class="slider-label" style="top:' . $pos . 'px">' . $row['text'] . '</div>';
		}
		
		if ($showTicks) {
			if ($row['value'] <> $maxVal) {
				$pos = $fullHeight / $maxVal * ($maxVal - $row['value']);
				$labels .= '<div class="slider-scale" style="top:' . $pos . 'px"></div>';
			}
		}
	}
}

?>

<div id="slider-area-<?= $uid ?>" class="slider-area" style="height:<?= $fullHeight ?>px;">
	<?php if (count($answers) <= 2): ?>
		<div class="slider-label" style="top:-6px; left:45px;"><?= $answers[0]['text'] ?></div>
		<div class="slider-label" style="top:<?= ($fullHeight - 6) ?>px; left:45px;"><?= $answers[1]['text'] ?></div>
	<?php endif; ?>
	<div id="slider-box-<?= $uid ?>" class="slider-box" style="height:<?= $fullHeight ?>px;"></div>
	<div id="slider-handle-<?= $uid ?>" class="slider-handle" style="top:-5px; display:none;"></div>
	<div class="slider-scale-end" style="top:0px;"></div>
	<?= $labels ?>
	<div class="slider-scale-end" style="top:<?= ($fullHeight) ?>px;"></div>
</div>

<script type="text/javascript">
	
	slider_<?= $uid ?> = new Object;

	slider_<?= $uid ?>.firstTouch = true;

	slider_<?= $uid ?>.overlap = $('#slider-handle-<?= $uid ?>').height() / 2;
	slider_<?= $uid ?>.maxPos = <?= $fullHeight ?>;
	slider_<?= $uid ?>.maxVal = <?= $maxVal ?>;

	slider_<?= $uid ?>.moving = false;
	slider_<?= $uid ?>.boxTop = 0;

	refreshValue_<?= $uid ?>(0);

	$('#slider-box-<?= $uid ?>').click(function(e) {
		if (slider_<?= $uid ?>.firstTouch) {
			$('#slider-handle-<?= $uid ?>').show();

			var newPos =  e.pageY - $('#slider-box-<?= $uid ?>').offset().top - slider_<?= $uid ?>.overlap;
			$('#slider-handle-<?= $uid ?>').css('top', newPos);
			
			refreshValue_<?= $uid ?>(1);

			slider_<?= $uid ?>.firstTouch = false;
		}
	});

	$('#slider-handle-<?= $uid ?>').mousedown(function (e) {
		slider_<?= $uid ?>.moving = true;
		slider_<?= $uid ?>.boxTop = $('#slider-box-<?= $uid ?>').offset().top;
		$('#slider-box-<?= $uid ?>').css('cursor', 'row-resize');
		return false;
	})

	$(document).mouseup(function () {
		slider_<?= $uid ?>.moving = false;
		$('#slider-box-<?= $uid ?>').css('cursor', 'default');
	})

	$(document).mousemove(function (e) {
		if (slider_<?= $uid ?>.moving) {
			var newPos =  e.pageY - slider_<?= $uid ?>.boxTop - slider_<?= $uid ?>.overlap;
			if (newPos < -slider_<?= $uid ?>.overlap) newPos = - slider_<?= $uid ?>.overlap;
			if (newPos > slider_<?= $uid ?>.maxPos - slider_<?= $uid ?>.overlap) newPos = slider_<?= $uid ?>.maxPos - slider_<?= $uid ?>.overlap;
			$('#slider-handle-<?= $uid ?>').css('top', newPos);
			
			refreshValue_<?= $uid ?>(1);
		}
	});

	function refreshValue_<?= $uid ?>(answered) {
		var newPos = $('#slider-handle-<?= $uid ?>').position().top + slider_<?= $uid ?>.overlap;
		var newVal = Math.round(1000 - (newPos / slider_<?= $uid ?>.maxPos * 1000));

		$('input[name=value-<?= $uid ?>]').val(newVal);
		$('input[name=text-<?= $uid ?>]').val(getTextFromValue_<?= $uid ?>(newVal));
		$('input[name=answered-<?= $uid ?>]').val(answered);
	}
	
	function getTextFromValue_<?= $uid ?>(val) {
		if (1 == 0) {
			// nothing
		} 
		<?php 
		foreach($answers as $row):
			$val = round($row['value'] / $maxVal * 1000);
		?>
		else if (val <= <?= $val ?>) { return "<?= $row['text']; ?>"; }	
		<?php endforeach; ?>
	}
</script>

