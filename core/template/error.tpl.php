<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>QualityCrowd 2</title>

		<link rel="stylesheet" href="<?= BASE_URL ?>core/files/css/style.css" />

		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/jquery.js"></script>
	</head>
	<body>
		<div class="header">
		</div>

		<h1>Error!</h1>
		<p>The following error occured:<p>
		<code><?= $message ?></code>
		<?php if(isset($trace)): ?>
		<br/><br/>
		<code><?= $trace ?></code>
		<?php endif; ?>
		<p>Restart the test to try again or contact the test supervisor.<p>

		<div class="footer">
			<button id="button_restart">Restart</button>
		</div>

		<script type="text/javascript">
			$('#button_restart').click(function() {
				var href = window.location.href;
				href = href.replace(/\?restart=$/g, '');
				window.location.href = href + '?restart='; 
			});
		</script>
	</body>
</html>
