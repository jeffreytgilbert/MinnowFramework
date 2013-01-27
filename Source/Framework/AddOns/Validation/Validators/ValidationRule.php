<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

abstract class ValidationRule{
	
	const INVALID_VALUE_IN_GOOD_LIST = 'INVALID_VALUE_IN_GOOD_LIST';
	const INVALID_VALUE_IN_BAD_LIST = 'INVALID_VALUE_IN_BAD_LIST';
	const INVALID_VALUE_IS_REQUIRED = 'INVALID_VALUE_IS_REQUIRED';
	const INVALID_VALUE_CANNOT_BE_EMPTY = 'INVALID_VALUE_CANNOT_BE_EMPTY';
	const INVALID_VALUE_MATCH = 'INVALID_VALUE_MATCH';
	
	// data should be read only
	private
		$_data,
		$_errors = array();
	
	// message can be read write, but only from within 
	protected 
		$_message;
	
	public function __construct($data=null){ $this->_data = $data; }
	
	public static function cast(ValidationRule $ValidationRule){
		return $ValidationRule;
	}
	
	public function getData(){ return $this->_data; }
	
	protected function throwException($message, $code=null){
		$this->_errors[$message] = $code; // accumulate a list of errors for each validator
		throw new Exception($message, $code);
	}
	
	public function getErrors(){ return $this->_errors; }
	
	public function notEmpty(){
		$data = trim($this->getData());
		if( empty($data) ){
			$this->throwException(self::INVALID_VALUE_CANNOT_BE_EMPTY);
		}
		return $this;
	}
	
	public function required(){
		$data = $this->getData();
		if( is_null($data) || $data == '' ){
			$this->throwException(self::INVALID_VALUE_IS_REQUIRED);
		}
		return $this;
	}
	
	public function in(Array $array){
		if( !in($this->getData(), $array) ){
			$this->throwException(self::INVALID_VALUE_IN_GOOD_LIST);
		}
		return $this;
	}
	
	public function notIn(Array $array){
		if( in($this->getData(), $array) ){
			$this->throwException(self::INVALID_VALUE_IN_BAD_LIST);
		}
		return $this;
	}
	
	public function matches($string){
		if( $this->getData() == $string ){ // just greater than
			$this->throwException(self::INVALID_VALUE_MATCH);
		}
		return $this;
	}
	
}