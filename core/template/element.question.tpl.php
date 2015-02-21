<?php
$style = '';

if ($width > 0) {
	$style = "width:{$width}px; float:left; margin-left:10px";
}

?>
<fieldset style="<?= $style ?>">
	<legend><?= $question ?></legend>
	<?= $answerform ?>
</fieldset>
