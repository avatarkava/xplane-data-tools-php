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

	public function outputVRC()
	{
		// @TODO - Implement a templating system on this
		include ('templates/vrc.tpl');
	}

}
?>