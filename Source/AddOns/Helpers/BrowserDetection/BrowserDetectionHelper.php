<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class BrowserDetectionHelper extends Helper{
	
	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		Run::fromHelpers('BrowserDetection/Requirements/BrowserDetection.php');
		
		$this->_instance = new BrowserDetection(
			$this->getConfig()->get('debug')
		);
	}
	
	public function getInstance(){
		if($this->_instance instanceof BrowserDetection) return $this->_instance;
		return new BrowserDetection();
	}
	
	public function __destruct(){
		// unset($this);
	}
}
