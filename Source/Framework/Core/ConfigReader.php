<?php

trait ConfigReader{
	
	// config cache for app settings from ini's
	private $_config;
	
	// @todo this config call could need to change based on which class called it (Call that Service) and then make grouping optional
	public function config($folder='', $grouping=null, $setting=null){ // if setting is null, return array
		
// 		print_r($this->_config);
		
		try{
			// Not an actual file read each time. This method checks a cached copy first
			if(!isset($this->_config[$folder])){
				if(!empty($folder)){
						$this->_config[$folder] = SettingsRegistry::get(
							$folder.RuntimeInfo::instance()->getApplicationName().'.ini',
							Path::toAddOns().$folder.'example.ini'
						);
				} else {
					$this->_config[$folder] = SettingsRegistry::get(
						RuntimeInfo::instance()->getApplicationName().'.ini',
						Path::toFramework().'example.ini'
					);
				}
			}
		} catch(Exception $e){
			die($e->getMessage());
		}
		
		if(!is_null($grouping)){
			if(!isset($this->_config[$folder][$grouping])){
				//throw new Exception('Fatal Error: Could not load settings for grouping ["'.$grouping.'"] from file in: '.$folder);
				die('Fatal Error: Could not load settings for grouping ["'.$grouping.'"] from file in: '.$folder);
			}
			
			if(!is_null($setting)){
				return isset($this->_config[$folder][$grouping][$setting])?$this->_config[$folder][$grouping][$setting]:'';
			} else {
				return isset($this->_config[$folder][$grouping])?new Model($this->_config[$folder][$grouping]):new Model();
			}
		} else {
			if(!is_null($setting)){
				return isset($this->_config[$folder][$setting])?$this->_config[$folder][$setting]:'';
			} else {
				return isset($this->_config[$folder])?new Model($this->_config[$folder]):new Model();
			}
		}
	}

}
