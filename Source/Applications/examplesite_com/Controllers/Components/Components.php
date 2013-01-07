<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class Components{
	
	public static function cast(Components $Components){ return $Components; }
	
	// @todo i dont think this config reader is correct. the settings file shouldnt need to be an application name. 
	// in fact, maybe inside this function we check for a "settings.ini" as a failsafe. 
	// that way if ever there is a need for a global default settings file, one can be maintained without individual settings files needing to be created.
	
	use ConfigReader;
	private $_components = array();
	
	// make sure components are freed at the end of a process execution
	public function __destruct(){
		foreach($this->_components as $ComponentType){
			foreach ($ComponentType as $Component){
				if($Component instanceof Component){ $Component->__destruct(); }
			}
		}
	}
	
	// example usage: 
	// RuntimeInfo->Components->Authentication($this)->authenticate();
	
	// 	this is how connector/drivers should be installed. This code can be reused identically while just changing the instance name of each instance type
	
	public function Authentication(Controller $Controller, $instance_name='default'){
		if(isset($this->_components['Authentication'][$instance_name])
				&& $this->_components['Authentication'][$instance_name] instanceof AuthenticationComponent){
			return $this->_components['Authentication'][$instance_name]->getInstance();
		}
		Run::fromComponents('Authentication/AuthenticationComponent.php');
		$this->_components['Authentication'][$instance_name] = $AuthenticationComponent = new AuthenticationComponent($Controller, $this->config('Controllers/Components/Authentication/', $instance_name));
		return $AuthenticationComponent->getInstance();
	}
	
// if it were single config
// 	public function Authentication(){
// 		if(isset($this->_components['Authentication'])
// 				&& $this->_components['Authentication'] instanceof AuthenticationComponent){
// 			return $this->_components['Authentication']->getInstance();
// 		}
// 		Run::fromComponents('Authentication/AuthenticationComponent.php');
// 		$this->_components['Authentication'] = $AuthenticationComponent = new AuthenticationComponent($this->config('Components/Authentication/'));
// 		return $AuthenticationComponent->getInstance();
// 	}
	
}


