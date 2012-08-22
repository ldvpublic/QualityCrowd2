<ul class="submenu">
	<li <?= ($subpage == '' ? 'class="active"' : '') ?>>
		<a href="<?= BASE_URL ?>admin/doc">Introduction</a>
	</li>
	<li <?= ($subpage == 'reference' ? 'class="active"' : '') ?>>
		<a href="<?= BASE_URL ?>admin/doc/reference">Command Reference</a>
	</li>
</ul>

<?= $content ?>