<?php if ($message <> ''): ?>
<div class="successmessage">
	<?= $message ?>
</div>
<?php endif; ?>

<h3>Cache</h3>

<button id="button_cleancache">Clean all caches</button>

<script>
	$('#button_cleancache').click(function() 
	{
		var url = '<?= BASE_URL ?>admin/maintenance/cleancache';
		window.location.href = url;
	});
</script>

<h3>Setup</h3>

<button id="button_setup">Run setup script again</button>

<p>Repairs file permissions, creates missing directories and htacces-files. Does not delete any user data.</p>

<script>
	$('#button_setup').click(function() 
	{
		var url = '<?= BASE_URL ?>admin/maintenance/setup';
		window.location.href = url;
	});
</script>