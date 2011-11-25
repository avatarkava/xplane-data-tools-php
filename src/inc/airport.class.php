<?php
class Airport
{
	var $atc;
	var $elevation;
	var $icao;
	var $name;
	var $geo;

	var $freq_twr, $freq_gnd;

	var $runways = array();
	var $taxiways = array();

	public function __construct()
	{
	}

	public function addRunway($data)
	{
		$this->runways[] = $data;
		return;
	}

	public function addTaxiway($data)
	{
		$this->taxiways[] = $data;
		return;
	}

	public function addTaxiwayNodes($data)
	{
		$last = array_pop($this->taxiways);
		$last['nodes'][] = $data;
		$this->taxiways[] = $last;
	}

	public function output($format = 'sct2', $path = '')
	{
		if ($format != 'sct2') {
			die('Output format currently not supported');
		}

		if ($path == '') {
			$path = dirname(__FILE__) . '/../../output/' . $this->icao . '.' . $format;
		}

		// @TODO - Implement a templating system on this
		ob_start();
		include ('../templates/vrc.tpl');
		$output = ob_get_contents();
		ob_end_clean();

		if ($output != '') {
			file_put_contents($path, $output);
			echo 'Output saved to ' . $path . "\n";
		}
		return;
	}

}
?>