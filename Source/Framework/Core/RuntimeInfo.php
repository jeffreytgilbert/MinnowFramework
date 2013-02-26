<?php

final class RuntimeInfo{
	public static function instance(Startup $Startup=null){
		static $instance = null;
		if(!is_null($Startup)) { // if startup is supplied for storage, set it every time
			$instance = $Startup;
			return $Startup;
		} else { // if none is loaded and none is supplied, return null
			return $instance;
		}
	}
}
