<?php

class LoggerAbstractionXYZ {
	
	const _DEBUG = 1;
	const _INFO = 2;
	const _NOTICE = 3;
	const _WARNING = 4;
	const _ERROR = 5;
	const _CRITICAL = 6;
	const _ALERT = 7;
	const _EMERGENCY = 8;
	
	public function __construct($debug_level, $log_to_table=false, $log_to_browsers=false, $log_to_file=false){
		
		if(!in(strtolower($default_format), array('png','jpg','gif'))){
			die('Unsupported default output format. Choose png, jpg, or gif.');
		}
		
		if(!in(ucfirst($interface), array('Gd','Imagick','Gmagick'))){
			die('Unsupported interface type. Choose Gd, Imagick, or Gmagick.');
		}
		
		$this->_default_format = strtolower($default_format);
		$this->_interface = ucfirst($interface);
		$this->_debug = $debug;
	}
	
}