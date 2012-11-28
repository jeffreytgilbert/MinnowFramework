<?php

class SessionAbstraction {
	
	private $_sessionConfig;
	
	public static function cast(SessionAbstration $SessionAbstraction){ return $SessionAbstraction; }
	
	public function __construct(SessionConfig $SessionConfig){
		$this->_sessionConfig = $SessionConfig;
	}
	
	public function getSessionConfig(){
		return $this->_sessionConfig;
	}
	
	public function write($label, $data){
		$app_name = RuntimeInfo::instance()->getApplicationName();
		if(isset($_SESSION[$app_name]) && isset($_SESSION[$app_name]['Data'])){
			$session_data = $_SESSION[$app_name]['Data'];
		} else {
			$session_data = array();
		}
		
		$session_data[$label]=$data;
		
		$_SESSION[$app_name]['Data'] = $session_data;
		
		return $this;
	}
	
	public function read($label){
		$app_name = RuntimeInfo::instance()->getApplicationName();
		return (isset($_SESSION[$app_name]) &&
				isset($_SESSION[$app_name]['Data']) && 
				isset($_SESSION[$app_name]['Data'][$label]))?:$_SESSION[$app_name]['Data'][$label];null;
	}
	
	private function setMessage($type, $message, $code='flash') {
		$app_name = RuntimeInfo::instance()->getApplicationName();
		if(isset($_SESSION[$app_name]) && isset($_SESSION[$app_name]['SystemMessage'])){
			$system_messages = $_SESSION[$app_name]['SystemMessage'];
		} else {
			$system_messages = array(
				'Notices'=>array(),
				'Errors'=>array(),
				'Confirmations'=>array()
			);
		}
		
		$these_messages = isset($system_messages[$type])?$system_messages[$type]:array();
		array_merge($these_messages, array($code => $these_messages));
		$system_messages[$type] = $these_messages;
		$_SESSION[$app_name]['SystemMessages'] = $system_messages;
		
		return $this;
	}
	
	public function setNotice($message, $code='flash'){
		self::setMessage('Notices',$message,$code);
		return $this;
	}
	
	public function setError($message, $code='flash'){
		self::setMessage('Errors',$message,$code);
		return $this;
	}
	
	public function setConfirmation($message, $code='flash'){
		self::setMessage('Confirmations',$message,$code);
		return $this;
	}
	
	public function flushMessages(){
		$app_name = RuntimeInfo::instance()->getApplicationName();
		if(isset($_SESSION[$app_name]['SystemMessages'])){
			$system_messages = $_SESSION[$app_name]['SystemMessages'];
			unset($_SESSION[$app_name]['SystemMessages']);
			return $system_messages;
		} else {
			return array(
				'Notices'=>array(),
				'Errors'=>array(),
				'Confirmations'=>array()
			);
		}
		return $this;
	}
}

