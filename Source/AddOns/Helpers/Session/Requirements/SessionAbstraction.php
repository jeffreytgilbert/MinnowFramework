<?php

class SessionAbstraction {
	
	private $_sessionConfig;
	
	public static function cast(SessionAbstraction $SessionAbstraction){ return $SessionAbstraction; }
	
	public function __construct(SessionConfig $SessionConfig){
		$this->_sessionConfig = $SessionConfig;
	}
	
	public function getSessionConfig(){
		return $this->_sessionConfig;
	}
	
	public function start(){ if(session_id() == '') { session_start(); } }
	
	public function write($label, $data){
		$app_name = RuntimeInfo::instance()->getApplicationName();
		$session_name = 'MINNOW::APPLICATION::'.upper($app_name).'::DATA';
		
		if(isset($_SESSION[$session_name])){
			$session_data = unserialize($_SESSION[$session_name]);
		} else {
			$session_data = array();
		}
		
		$session_data[$label]=$data;
		
		$_SESSION[$session_name] = serialize($session_data);
		
		return $this;
	}
	
	public function read($label){
		$app_name = RuntimeInfo::instance()->getApplicationName();
		$session_name = 'MINNOW::APPLICATION::'.upper($app_name).'::DATA';
		if(isset($_SESSION[$session_name])){
			$session_data = unserialize($_SESSION[$session_name]);
		} else { return null; }
		return (isset($session_data[$label]))?$session_data[$label]:null;
	}
	
	private function setMessage($type, $message, $code='flash') {
		$app_name = RuntimeInfo::instance()->getApplicationName();
		$session_name = 'MINNOW::APPLICATION::'.upper($app_name).'::MESSAGES';
		
		if(isset($_SESSION[$session_name])){
			$system_messages = unserialize($_SESSION[$session_name]);
		} else {
			$system_messages = array(
				'Notices'=>array(),
				'Errors'=>array(),
				'Confirmations'=>array()
			);
		}
		
		$messages_for_type = isset($system_messages[$type])?$system_messages[$type]:array();
		$messages_for_type = array_merge($messages_for_type, array($code => $message));
		$system_messages[$type] = $messages_for_type;
		
		$_SESSION[$session_name] = serialize($system_messages);
		
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
		$session_name = 'MINNOW::APPLICATION::'.upper($app_name).'::MESSAGES';
		
		if(isset($_SESSION[$session_name])){
			$system_messages = unserialize($_SESSION[$session_name]);
			unset($_SESSION[$session_name]);
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

