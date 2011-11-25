<?php
/**
 * Currently just outputs .sct file compatible data for a given airport
 * @todo - Command online only right now
 */
require_once ('inc/util.class.php');
require_once ('inc/airport.class.php');

if (isset($argv[1])) {
	$icao = strtoupper(trim($argv[1]));
}
else {
	die("You must specify an airport code on the command line!\n\n");
}

$basedir = dirname(__FILE__);
$handle = fopen($basedir . '/../data/' . $icao . '.dat', 'r') or die('Airport ' . $icao . ' not found!');

while (!feof($handle)) {
	$buffer = fgets($handle, 4096);
	$line = trim($buffer);

	// Skip blank lines
	if ($line == '') {
		continue;
	}

	$results = Util::tokenizeLine($line);
	$rowType = $results[0];

	//echo $line . "\n";

	// @FIXME - Refactor this into the class
	switch($rowType) {
		/*
		 * 1 - Land Airport
		 * 16 - Seaplane Base
		 * 17 - Heliport
		 */
		case 1 :
		case 16 :
		case 17 :
		/**
		 * 1 - Elevation in feet AMSL
		 * 2 - Control Tower Flag (0 = no tower, 1 = has tower)
		 * 3 - Deprecated - use 0 always
		 * 4 - ICAO
		 * 5 - Airport name
		 */
			$Airport = new Airport();
			$Airport->elevation = $results[1];
			$Airport->atc = $results[2];
			$Airport->icao = $results[4];
			$Airport->name = join(' ', array_slice($results, 5));
			break;

		/**
		 * 100 - Land Runway
		 * 101 - Water Runway
		 * 102 - Helipad
		 */
		case 100 :
			$data = array();
			$data['type'] = $rowType;
			$data['width'] = $results[1];
			$data['surface'] = $results[2];
			$data['shoulder'] = $results[3];
			$data['smoothness'] = $results[4];
			$data['center_line_lights'] = $results[5];
			$data['edge_lighting'] = $results[6];
			$data['autogen_distance_remaining_signs'] = $results[7];
			$bearing = Util::bearing($results[9] . ' ' . $results[10], $results[18] . ' ' . $results[19]);			
			$data['endpoint'][0] = array(
					'number' => $results[8],
					'centerline_geo' => $results[9] . ' ' . $results[10],	
					'bearing' => $bearing,					
					'displaced_threshold' => $results[11],
					'overrun' => $results[12],
					'runway_markings' => $results[13],
					'approach_lighting' => $results[14],
					'tdz_lighting' => $results[15],
					'reil_lighting' => $results[16],
			);
			$data['endpoint'][1] = array(
					'number' => $results[17],
					'centerline_geo' => $results[18] . ' ' . $results[19],					
					'bearing' => Util::newBearing($bearing, 180),
					'displaced_threshold' => $results[20],
					'overrun' => $results[21],
					'runway_markings' => $results[22],
					'approach_lighting' => $results[23],
					'tdz_lighting' => $results[24],
					'reil_lighting' => $results[25],
			);
			
			// Get 'corners' of the runway
			$sideLine = Util::destPoint($data['endpoint'][0]['centerline_geo'], $data['width']/5280, Util::newBearing($bearing, 90));
			$sideLine2 = Util::destPoint($data['endpoint'][0]['centerline_geo'], $data['width']/5280, Util::newBearing($bearing, -90));
			$data['endpoint'][0]['sideline1_geo'] = $sideLine;
			$data['endpoint'][0]['sideline2_geo'] = $sideLine2;
			$sideLine = Util::destPoint($data['endpoint'][1]['centerline_geo'], $data['width']/5280, Util::newBearing($bearing, 90));
			$sideLine2 = Util::destPoint($data['endpoint'][1]['centerline_geo'], $data['width']/5280, Util::newBearing($bearing, -90));
			$data['endpoint'][1]['sideline1_geo'] = $sideLine;
			$data['endpoint'][1]['sideline2_geo'] = $sideLine2;
			
			$Airport->addRunway($data);
			unset($data);
			break;
		case 101 :
		case 102 :
			break;
		case 110 :
		// Taxiway
			$data = array();
			$data['surface'] = $results[1];
			$data['smoothness'] = $results[2];
			$data['orientation'] = $results[3];
			$data['description'] = join(' ', array_slice($results, 4));
			$Airport->addTaxiway($data);
			$Airport->activeTaxiway = true;
			unset($data);
			break;

		// Linear feature - painted surface markings, light strings
		// Airport boundary - for terrain flattening
		case 120 :
		case 130 :
			$Airport->activeTaxiway = false;
			break;

		// Node points
		case 111 :
		case 112 :
		case 113 :
		case 114 :
		case 115 :
		case 116 :
			$data = array();
			$data['type'] = $rowType;
			$data['geo'] = $results[1] . ' ' . $results[2];					
			$latitude_bezier = (isset($results[3])) ? : '';
			$longitude_bezier = (isset($results[4])) ? : '';
			$data['bezier_geo'] = $latitude_bezier . ' ' . $longitude_bezier;
			$data['line_type'] = (isset($results[5])) ? : '';
			$data['lighting_type'] = (isset($results[6])) ? : '';
			if ($Airport->activeTaxiway) {
				$Airport->addTaxiwayNodes($data);
			}
			unset($data);
			break;
		// Viewpoint
		case 14 :
			break;

		// Startup location
		case 15 :
			break;
		// Light beacon
		case 18 :
			$Airport->geo = $results[1] . ' ' . $results[2];			
			break;

		// Windsock
		case 19 :

		// Taxiway or runway distance signs
		case 20 :

		// Lighting objects (VASI, PAPI, etc)
		case 21 :

		// Recorded ATC (ASOS/AWOS/ATIS)
		case 50 :
			$Airport->freq_atis = $results[1] / 100;
			break;

		// ATC - Unicom
		case 51 :
			$Airport->freq_ctaf = $results[1] / 100;
			break;

		// ATC - CLD
		case 52 :
			$Airport->freq_cld = $results[1] / 100;
			break;

		// ATC - GND
		case 53 :
			$Airport->freq_gnd = $results[1] / 100;
			break;

		// ATC - TWR
		case 54 :
			$Airport->freq_twr = $results[1] / 100;
			break;

		// ATC - APP
		case 55 :
			$Airport->freq_app = $results[1] / 100;
			break;

		// ATC - DEP
		case 56 :
			$Airport->freq_dep = $results[1] / 100;
			break;

		default :
			break;
	}

}
fclose($handle);
echo $Airport->output();
echo "\n";
exit();
?>