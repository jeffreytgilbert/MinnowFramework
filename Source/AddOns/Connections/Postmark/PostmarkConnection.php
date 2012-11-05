<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

final class PostmarkConnection extends Connection{

	public function __construct(Model $Config, $connection_name='default'){
		// run parent construct action to save settings
		parent::__construct($Config, $connection_name);

		Run::fromConnections('Postmark/Requirements/Postmark.php');
		
		// Check for CURL
		if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
			exit("\nERROR: CURL extension not loaded\n\n");
	
		$this->_instance = $Postmark = new Postmark(
			$this->getConfig()->get('api_key'),
			$this->getConfig()->get('from_address'),
			$this->getConfig()->get('reply_to_address')
		);
	}
	
	public function getInstance(){ 
		if($this->_instance instanceof Postmark) return $this->_instance;
		return new Postmark();
	}
	
	public function __destruct(){
		// unset($this);
	}
}
