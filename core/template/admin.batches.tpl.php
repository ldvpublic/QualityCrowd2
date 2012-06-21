<h2>Batches</h2>
<ul class="batchlist">
<?= $batchlist ?>
</ul>

<button id="button_new">New Batch</button>

<script>
	$('#button_new').click(function() 
	{
		var batchId = prompt("Enter a name for the new batch:", "");
		var url = '<?= BASE_URL ?>admin/batch/' + batchId + '/new';
		window.location.href = url;
	});
</script>

