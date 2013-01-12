<?php

class PHPMailerAbstraction extends PHPMailer {
	
	protected $_Config;
	
	public function setConfig(Model $Config){
		$this->_Config = $Config;
	}
	
	public function getConfig(){ if($this->_Config instanceof Model){ return $this->_Config;} else { return new Model(); } }
}
