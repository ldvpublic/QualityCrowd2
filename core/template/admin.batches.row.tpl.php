<li class="batchrow">
	<a href="<?= BASE_URL ?>admin/batch/<?= $id ?>">
		<div class="infofloat finished"><?= $finished ?></div>
		<div class="infofloat workers"><?= $workers ?></div>
		<div class="infofloat steps"><?= $steps ?></div>
		<div class="infofloat state">
			<div class="icon icon-<?= $state ?>" title="<?= Batch::readableState($state) ?>"></div>
		</div>
		
		<div class="id"><?= $id ?></div>
		<div class="title"><?= $title ?></div>
	</a>
</li>
