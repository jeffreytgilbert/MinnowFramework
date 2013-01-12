<?php

// this one needs to have the sessions config stored in the config (which should extend model) and then call its settings from within if using db as the config

final class EmailHelper extends Helper{

	public function __construct(Model $Config){
		// run parent construct action to save settings
		parent::__construct($Config);
		
		Run::fromHelpers('Email/Requirements/class.phpmailer.php');
		Run::fromHelpers('Email/Requirements/PHPMailerAbstraction.php');
		
		$this->_instance = new PHPMailerAbstraction();
		$this->_instance->setConfig($Config);
	}

	public function getInstance(){
		if($this->_instance instanceof PHPMailerAbstraction) return $this->_instance;
		return new PHPMailerAbstraction();
	}

	public function __destruct(){
		// unset($this);
	}
}
