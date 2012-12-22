<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class HybridAuthHelper extends Helper{

	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		Run::fromHelpers('HybridAuth/Requirements/Hybrid/Auth.php');
		Run::fromHelpers('HybridAuth/Requirements/HybridAuthAbstraction.php');
		Run::fromHelpers('HybridAuth/Requirements/HybridAuthException.php');
		
		$SettingsObject = $this->getConfig();
		
		$settings_array = $SettingsObject->toArray();
		
		$HybridAuthConfig = array();
		
		$HybridAuthConfig = $settings_array['HybridAuth'];
		unset($settings_array['HybridAuth']);
		
		$HybridAuthConfig['providers'] = array();
		
		foreach($settings_array as $key => $value){
			$enabled = $value['enabled'];
			unset($value['enabled']);
			$HybridAuthConfig['providers'][$key]['enabled'] = $enabled;
			if(count($value) > 0){ $HybridAuthConfig['providers'][$key]['keys'] = $value; }
		}
		
		$this->_instance = new HybridAuthAbstraction( $HybridAuthConfig );
	}

	public function getInstance(){
		if($this->_instance instanceof HybridAuthAbstraction) return $this->_instance;
		return new HybridAuthAbstraction();
	}

	public function __destruct(){
		// unset($this);
	}
}
