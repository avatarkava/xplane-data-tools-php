<?php
/**
 * Breaks up the apt.dat file into individual .dat files by airport code
 */
require_once ('inc/util.class.php');
require_once ('inc/airport.class.php');

if (!is_dir(dirname(__FILE__) . '/output')) {
	mkdir(dirname(__FILE__) . '/output');
}

$handle = fopen('apt.dat', 'r') or die('Unable to open airport data file - make sure apt.dat is in the same folder as this script!');
$counter = 0;
$curICAO = '';
$output = '';

while (!feof($handle)) {

	$buffer = fgets($handle, 4096);
	$line = trim($buffer);

	// Skip the first two lines of comments
	if ($line == '') {
		if ($output != '') {
			$outfile = dirname(__FILE__) . '/output/' . $curICAO . '.dat';
			echo '[' . $counter . '] Writing to ' . $outfile . "\n";
			$results = file_put_contents($outfile, $output);
		}
		$curICAO = '';
		$output = '';
		continue;
	}

	$output .= $line . "\n";
	$results = Util::tokenizeLine($line);
	$rowType = $results[0];
	$rowICAO = (!isset($results[4])) ? : '';

	switch($rowType) {
		/*
		 * 1 - Land Airport
		 * 16 - Seaplane Base
		 * 17 - Heliport
		 */
		case 1 :
		case 16 :
		case 17 :
			$Airport = new Airport();
			$Airport->elevation = $results[1];
			$Airport->atc = $results[2];
			$Airport->icao = $results[4];
			$Airport->name = join(' ', array_slice($results, 5));
			$counter++;
			$curICAO = $Airport->icao;
			break;
	}

}
exit();
?>