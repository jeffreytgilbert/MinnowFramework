<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class SecureHashHelper extends Helper{
	
	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		Run::fromHelpers('SecureHash/Requirements/SecureHash.php');
		
		$this->_instance = new SecureHash(
			$this->getConfig()->get('debug'),
			$this->getConfig()->get('encryption_type'),
			$this->getConfig()->get('encryption_iterations'),
			$this->getConfig()->get('encryption_salt_bytes'),
			$this->getConfig()->get('encryption_hash_bytes')
		);
	}
	
	public function getInstance(){
		if($this->_instance instanceof SecureHash) return $this->_instance;
		return new SecureHash();
	}
	
	public function __destruct(){
		// unset($this);
	}
}
