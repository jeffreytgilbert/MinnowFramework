<?php 

class ValidPassword extends ValidString{
	
 	const INVALID_PASSWORD_IS_WEAK = 'INVALID_PASSWORD_IS_WEAK';
	
	public function strong(){
		if(!preg_match('/^(?=^.{8,}$)((?=.*[A-Za-z0-9])(?=.*[A-Z])(?=.*[a-z]))^.*$/', $this->getData())){
			$this->throwException(self::INVALID_PASSWORD_IS_WEAK);
		}
		return $this;
	}
	
	public function validate(){
		$this->minLength(6);
		return $this;
	}
}