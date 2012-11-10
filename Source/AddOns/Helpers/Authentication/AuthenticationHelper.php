<?php

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class AuthenticationHelper extends Helper{

	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		require_once('phar://'.Path::toHelpers().'Authentication/Requirements/imagine-v0.3.0.phar');
		Run::fromHelpers('Authentication/Requirements/AuthenticationAbstraction.php');
		
		$this->_instance = new AuthenticationAbstraction(
			$this->getConfig()->get('default_format'),
			$this->getConfig()->get('interface'),
			$this->getConfig()->get('debug')
		);
	}

	public function getInstance(){
		if($this->_instance instanceof AuthenticationAbstraction) return $this->_instance;
		return new AuthenticationAbstraction();
	}

	public function __destruct(){
		// unset($this);
	}
}
