<?php

class LocationHelper extends Helper{

	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);

		Run::fromHelpers('Location/Requirements/Location.php');
		Run::fromHelpers('Location/Requirements/LocationFromIp.php');
		Run::fromHelpers('Location/Requirements/NetworkAddress.php');
		Run::fromHelpers('Location/Requirements/LocationServices.php');
		
		$this->_instance = new LocationServices(
			$this->_Config->get('debug')
		);
		
	}
	
	public function getInstance(){
		if($this->_instance instanceof LocationServices) return $this->_instance;
		return new LocationServices();
	}

	public function __destruct(){
		// unset($this);
	}

}