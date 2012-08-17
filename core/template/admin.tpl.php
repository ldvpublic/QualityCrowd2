<!doctype html>
<html>
	<head>
		<meta charset="utf-8">

		<title>QualityCrowd 2</title>

		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/swfobject.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/flashver.js"></script>
		<script type="text/javascript" src="<?= BASE_URL ?>core/files/js/jquery.js"></script>

		<link rel="stylesheet" href="<?= BASE_URL ?>core/3p/codemirror/lib/codemirror.css">
		<link rel="stylesheet" href="<?= BASE_URL ?>core/3p/codemirror/theme/ambiance.css">
    	<script src="<?= BASE_URL ?>core/3p/codemirror/lib/codemirror.js"></script>
    	<script src="<?= BASE_URL ?>core/3p/codemirror/mode/qc-script/qc-script.js"></script>

		<link rel="stylesheet" href="<?= BASE_URL ?>core/files/css/admin.css" />
    </head>
	<body>
		<div class="header">
			<h1>QualityCrowd</h1>
			<ul class="menu">
				<li <?= ($page == 'batches' || $page == 'batch' ? 'class="active"' : '') ?>>
					<a href="<?= BASE_URL ?>admin/batches">Batches</a>
				</li>
				<li <?= ($page == 'doc' ? 'class="active"' : '') ?>> 
					<a href="<?= BASE_URL ?>admin/doc">Documentation</a>
				</li>
				<li <?= ($page == 'maintenance' ? 'class="active"' : '') ?>> 
					<a href="<?= BASE_URL ?>admin/maintenance">Maintenance</a>
				</li>
			</ul>
		</div>

		<?= $content ?>

		<div class="footer">
			User: <?= $username ?>
		</div>
	</body>
</html>