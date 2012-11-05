<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

final class AmazonS3Connection extends Connection{

	public function __construct(Model $Config, $connection_name='default'){
		// run parent construct action to save settings
		parent::__construct($Config, $connection_name);

		Run::fromConnections('AmazonS3/Requirements/S3.php');
		
		// Check for CURL
		if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
			die("\nERROR: CURL extension not loaded\n\n");
		
		// Instantiate the class
		$this->_instance = $AmazonS3 = new S3(
			$this->_Config->getString('access_key_id'),
			$this->_Config->getString('secret_access_key'),
			false
		);
		$AmazonS3->setExceptions();
		
		return $AmazonS3;
		
	}
	
	// this method should be in the Action trait for this connection
	public function getS3URL(){
		return 'https://s3.amazonaws.com/'.$this->_Config->get('bucket_name').'/';
	}
	
	public function getInstance(){ 
		if($this->_instance instanceof S3) return $this->_instance;
		return new S3();
	}
	
	public function __destruct(){
		// unset($this);
	}
}

