<?php
if (count($workers) == 0) {
	echo "<p>No results available</p>";
	return;
}

require_once (ROOT_PATH .'core'.DS.'3p'.DS.'jpgraph'.DS.'src'.DS.'jpgraph.php');
require_once (ROOT_PATH .'core'.DS.'3p'.DS.'jpgraph'.DS.'src'.DS.'jpgraph_pie.php');
require_once (ROOT_PATH .'core'.DS.'3p'.DS.'phpbrowscap'.DS.'src'.DS.'phpbrowscap'.DS.'Browscap.php');

use phpbrowscap\Browscap;
//ini_set('memory_limit', '-1'); // may be required for initial downloading
$bc = new Browscap(TMP_PATH.'browscap');

$data = array(
	'browsers' => array(),
	'browsersF' => array(),
	'platforms' => array(),
	'platformsF' => array(),
);

foreach($workers as $w)
{
	if (!isset($w['useragent'])) continue;
	
	$browser = $bc->getBrowser($w['useragent']);
	$data['browsers'][] = $browser->Browser;
	$data['platforms'][] = $browser->Platform;

	if ($w['finished']) {
		$data['browsersF'][] = $browser->Browser;
		$data['platformsF'][] = $browser->Platform;
	}
}

foreach ($data as &$d) {
	$d = array_count_values($d);
	asort($d);
	$d['values'] = array_reverse($d);
}


foreach($data as $key => &$d)
{
	// setup the graph
	$graph = new PieGraph(400,300);
	$theme_class= new UniversalTheme;
	$graph->SetTheme($theme_class);
	
	$graph->img->SetAntiAliasing(true);
	$graph->SetBox(false);

	// plot pie
	$p1 = new PiePlot(array_values($d['values']));
	$graph->Add($p1);
	$p1->SetLegends(array_keys($d['values']));
	$p1->SetSize(0.38);
	$p1->SetCenter(0.3, 0.5);

	$graph->legend->SetColumns(1);
	$graph->legend->SetPos(0,0.5,'right', 'center');

	// output graph to temp file
	$graph->Stroke(TMP_PATH.'img-cache'.DS.$key.'-'.$id.'.png');
}

?>

<h3>Browsers</h3>
<h4>all workers</h4>
<img src="<?= BASE_URL.'core/tmp/img-cache/browsers-'.$id.'.png' ?>">
<h4>finished workers</h4>
<img src="<?= BASE_URL.'core/tmp/img-cache/browsersF-'.$id.'.png' ?>">

<h3>Platforms</h3>
<h4>all workers</h4>
<img src="<?= BASE_URL.'core/tmp/img-cache/platforms-'.$id.'.png' ?>">
<h4>finished workers</h4>
<img src="<?= BASE_URL.'core/tmp/img-cache/platformsF-'.$id.'.png' ?>">
