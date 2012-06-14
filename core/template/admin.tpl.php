<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>QualityCrowd 2</title>

		<link rel="stylesheet" href="<?= BASE_URL ?>core/files/css/style.css" />
		<link rel="stylesheet" href="<?= BASE_URL ?>core/files/css/admin.css" />

		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/swfobject.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/flashver.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/jquery.js"></script>
	</head>
	<body>
		<div class="header">
			<h1>QualityCrowd</h1>
			<ul class="menu">
				<li><a href="<?= BASE_URL ?>admin/batches">Batches</a></li>
				<li><a href="<?= BASE_URL ?>admin/doc">Documentation</a></li>
			</ul>
		</div>

		<?= $content ?>

		<div class="footer">
			User: <?= $username ?>
		</div>
	</body>
</html>