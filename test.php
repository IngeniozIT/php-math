<?php
declare(strict_types = 1);

require_once('vendor/autoload.php');

use IngeniozIT\Math\Random;
use IngeniozIT\Math\KMeans;

$values = [];

for ($i = 0; $i < 500; ++$i) {
	for ($j = 0; $j < 500; ++$j) {
		$delta = (($i % 5) - 2.5) * 100;
		$values[$i][$j] = Random::frand(-5 + $delta, 5 + $delta);
	}
}

$kMeans = new KMeans($values);
$kMeans->classifyAndOptimize();
echo $kMeans->nbClusters(), PHP_EOL;
