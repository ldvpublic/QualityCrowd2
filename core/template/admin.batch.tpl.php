<h2>Batch "<?= $id ?>"</h2>

<ul class="submenu">
	<li <?= ($subpage == '' ? 'class="active"' : '') ?>>
		<a href="<?= BASE_URL ?>admin/batch/<?= $id ?>">View</a>
	</li>
	<li <?= ($subpage == 'edit' ? 'class="active"' : '') ?>>
		<a href="<?= BASE_URL ?>admin/batch/<?= $id ?>/edit">Edit</a>
	</li>
	<li <?= ($subpage == 'validate' ? 'class="active"' : '') ?>>
		<a href="<?= BASE_URL ?>admin/batch/<?= $id ?>/validate">Validate Worker</a>
	</li>
	<li <?= ($subpage == 'results' ? 'class="active"' : '') ?>>
		<a href="<?= BASE_URL ?>admin/batch/<?= $id ?>/results">Results</a>
	</li>
	<li <?= ($subpage == 'browsers' ? 'class="active"' : '') ?>>
		<a href="<?= BASE_URL ?>admin/batch/<?= $id ?>/browsers">Browsers</a>
	</li>
</ul>

<?= $content ?>
