<input type="hidden" name="value" value="">
<input type="hidden" name="text" value="">
<input type="hidden" name="answermode" value="continous">
<input type="hidden" name="answered" value="0">

<?php

$maxVal = 0;
$fullHeight = 180;

foreach($answers as $row) {
	if ($row['value'] > $maxVal) $maxVal = $row['value'];
}

$labels = '';
$delta = $fullHeight / $maxVal;
foreach($answers as $row) {
	if ($row['text'] <> '') {
		$pos = $fullHeight / $maxVal * ($maxVal - $row['value']) + $delta /2 - 5;
		$labels .= '<div class="slider-label" style="top:' . $pos . 'px">' . $row['text'] . '</div>';
	}
	
	if ($row['value'] <> $maxVal) {
		$pos = $fullHeight / $maxVal * ($maxVal - $row['value']);
		$labels .= '<div class="slider-scale" style="top:' . $pos . 'px"></div>';
	}
}

?>

<style type="text/css">
div.slider-area {
	position:relative;
	height:<?= $fullHeight ?>px;
	margin:10px;
	padding:0;
}

div.slider-box {
	width:15px;
	height:<?= $fullHeight ?>px;
	position:absolute;
	top:0px;
	background-color:#ddd;
	left:12px;
}

div.slider-handle {
	width:20px;
	height:10px;
	background-color: red;
	border:1px solid #660000;
	position:absolute;
	cursor:row-resize;
	top:<?= $fullHeight / 2 - 5?>px;
	left:8px;
	border-radius:7px;
}

div.slider-label {
	position:absolute;
	left:40px;
	font-size:12px;
}

div.slider-scale {
	position:absolute;
	border-bottom:1px solid #888;
	width:32px;
	height:0px;
	left:3px;
}

div.slider-scale-end {
	position:absolute;
	border-bottom:2px solid #333;
	width:40px;
	height:0px;
}

</style>

<div id="slider-area" class="slider-area">
	<div id="slider-box" class="slider-box"></div>
	<div id="slider-handle" class="slider-handle"></div>
	<div class="slider-scale-end" style="top:0px;"></div>
	<?= $labels ?>
	<div class="slider-scale-end" style="top:<?= ($fullHeight) ?>px;"></div>
</div>

<script type="text/javascript">
	
	slider = new Object;
	
	slider.overlap = $('#slider-handle').height() / 2;
	slider.maxPos = <?= $fullHeight ?>;
	slider.maxVal = <?= $maxVal ?>;

	slider.moving = false;
	slider.boxTop = 0;

	refreshValue(0);

	$('#slider-handle').mousedown(function (e) {
		slider.moving = true;
		slider.boxTop = $('#slider-box').offset().top;
		$('#slider-box').css('cursor', 'row-resize');
		return false;
	})

	$(document).mouseup(function () {
		slider.moving = false;
		$('#slider-box').css('cursor', 'default');
	})

	$(document).mousemove(function (e) {
		if (slider.moving) {
			var newPos =  e.pageY - slider.boxTop - slider.overlap;
			if (newPos < -slider.overlap) newPos = - slider.overlap;
			if (newPos > slider.maxPos - slider.overlap) newPos = slider.maxPos - slider.overlap;
			$('#slider-handle').css('top', newPos);
			
			refreshValue(1);
		}
	});

	function refreshValue(answered) {
		var newPos = $('#slider-handle').position().top + slider.overlap;
		var newVal = Math.round(1000 - (newPos / slider.maxPos * 1000));

		$('input[name=value]').val(newVal);
		$('input[name=text]').val(getTextFromValue(newVal));
		$('input[name=answered]').val(answered);
	}
	
	function getTextFromValue(val) {
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

