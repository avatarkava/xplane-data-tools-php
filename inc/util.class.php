<?php

/**
 * $pointB = $Airport->runways[0]['endpoint'][0]['centerline_geo'];
 $pointA = $Airport->runways[0]['endpoint'][1]['centerline_geo'];
 echo $Airport->runways[0]['endpoint'][0]['number'] . "\n";
 echo Util::distanceBetween($pointA, $pointB);
 echo "\n";
 echo Util::bearing($pointA, $pointB);
 */
class Util
{
	const earthRadiusMiles = 3960.00;
	const mileFeet = 5280;

	public function tokenizeLine($line)
	{
		$tokenNum = 0;
		$results = array();
		$tok = strtok($line, " \n\t");
		while ($tok !== false) {
			$tokenNum++;
			$token = trim($tok);
			$results[] = $token;
			$tok = strtok(" \n\t");
		}

		return $results;
	}

	public function distanceBetween($pointA, $pointB, $format = 'decimal')
	{
		if ($format != 'decimal') {
			return 'Currently unsupported format';
		}
		list($aLat, $aLng) = explode(' ', $pointA);
		list($bLat, $bLng) = explode(' ', $pointB);
		$alpha = ($bLat - $aLat) / 2;
		$beta = ($bLng - $aLng) / 2;
		$a = sin(deg2rad($alpha)) * sin(deg2rad($alpha)) + cos(deg2rad($aLat)) * cos(deg2rad($bLat)) * sin(deg2rad($beta)) * sin(deg2rad($beta));
		$c = asin(min(1, sqrt($a)));
		$distance = 2 * self::earthRadius * $c;
		$distance = round($distance, 4);

		return $distance;
	}

	public function bearing($pointA, $pointB)
	{
		list($aLat, $aLng) = explode(' ', $pointA);
		list($bLat, $bLng) = explode(' ', $pointB);

		$aLat = deg2rad($aLat);
		$aLng = deg2rad($aLng);
		$bLat = deg2rad($bLat);
		$bLng = deg2rad($bLng);

		$bearing = (rad2deg(atan2(sin($bLng - $aLng) * cos($bLat), cos($aLat) * sin($bLat) - sin($aLat) * cos($bLat) * cos($bLng - $aLng))) + 360) % 360;
		return round($bearing);
	}

	public function midpoint($pointA, $pointB, $format = 'decimal')
	{

	}

	public function newBearing($angle, $skew)
	{
		$result = ($angle + $skew) % 360;
		if ($result < 0) {
			$result += 360;
		}
		return sprintf('%03d', $result);
	}

	public function destPoint($start, $distance, $bearing, $format = 'decimal')
	{
		if ($format != 'decimal') {
			return 'Currently unsupported format';
		}
		list($latitude, $longitude) = explode(' ', $start);
		$aLat = deg2rad($latitude);
		$aLng = deg2rad($longitude);
		$bearing = deg2rad($bearing);
		$distance = $distance / self::earthRadiusMiles;

		$bLat = asin(sin($aLat) * cos($distance) + cos($aLat) * sin($distance) * cos($bearing));
		$bLng = $aLng + atan2(sin($bearing) * sin($distance) * cos($aLat), cos($distance) - sin($aLat) * sin($bLat));
		$bLng = fmod(($bLng + 3 * pi()), (2 * pi())) - pi();

		return rad2deg($bLat) . ' ' . rad2deg($bLng);
	}

	public function dec2Dms($value)
	{
		list($latitude, $longitude) = explode(' ', $value);

		// Latitude
		$value = $latitude;
		$cardinal = ($value >= 0) ? 'N' : 'S';
		$degrees = abs($value);
		$fdegrees = floor($degrees);
		$minutes = ($degrees - $fdegrees) * 60;
		$fminutes = floor($minutes);
		$seconds = ($minutes - $fminutes) * 60;
		$flatitude = $cardinal . sprintf('%1$03d', $fdegrees) . '.' . sprintf('%1$02d', $fminutes) . '.' . sprintf('%06.3f', $seconds);

		// Longitude
		$value = $longitude;
		$cardinal = ($value >= 0) ? 'E' : 'W';
		$degrees = abs($value);
		$fdegrees = floor($degrees);
		$minutes = ($degrees - $fdegrees) * 60;
		$fminutes = floor($minutes);
		$seconds = ($minutes - $fminutes) * 60;
		$flongitude = $cardinal . sprintf('%1$03d', $fdegrees) . '.' . sprintf('%1$02d', $fminutes) . '.' . sprintf('%06.3f', $seconds);

		return $flatitude . ' ' . $flongitude;
	}

	public function dms2Dec($value)
	{
		list($degrees, $minutes, $seconds, $micro) = explode('.', $value);
		$decimal = $degrees + ($minutes / 60) + (($seconds + $micro / 1000) / 3600);
		return $decimal;
	}

}
?>