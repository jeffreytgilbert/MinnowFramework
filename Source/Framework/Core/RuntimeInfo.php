<?php

final class RuntimeInfo{
	public static function instance($application_name=null, Startup $Startup=null){
		static $RuntimeInfo = array();
		static $last_referenced = null;
		if(!is_null($Startup)) { // if startup is supplied for storage, set it every time
			$RuntimeInfo[$Startup->getApplicationName()] = $Startup;
			$last_referenced = $Startup->getApplicationName();
			return $Startup;
		} else if(count($RuntimeInfo) === 1){ // common to only have one application running at a time, so if there's only one loaded, return it
			$Startup = current($RuntimeInfo);
			$last_referenced = $Startup->getApplicationName();
			return $Startup;
		} else if(is_null($application_name) && !is_null($last_referenced)) { // if an application is in the process of starting up where there are multiple applications living, launch with the last used application
			return $RuntimeInfo[$last_referenced];
		} else { // if none is loaded and none is supplied, return null
			return null;
		}
	}
}
