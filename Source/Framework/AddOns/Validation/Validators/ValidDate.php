<?php 

class ValidDate extends ValidationRule{
	
	const INVALID_DATE_FORMAT = 'INVALID_DATE_FORMAT';
	const INVALID_DATE_BEYOND_LIMIT = 'INVALID_DATE_BEYOND_LIMIT';
	
	private $_DateTime;
	
	public function __construct($data){
		parent::__construct($data);
		if(strtotime($data) === false){
			$this->throwException(self::INVALID_DATE_FORMAT);
		} else {
			$this->_DateTime = new DateTime($data);
		}
	}
	
	public function before($date){
		if($this->_DateTime->getTimestamp() < strtotime($date)){
			$this->throwException(self::INVALID_DATE_BEYOND_LIMIT);
		}
		return $this;
	}
	
	public function after($date){
		if($this->_DateTime->getTimestamp() > strtotime($date)){
			$this->throwException(self::INVALID_DATE_BEYOND_LIMIT);
		}
		return $this;
	}
}