<?php 

class ValidSocialSecurityNumber extends ValidString{
	
	const INVALID_SOCIAL_SECURITY_NUMBER = 'INVALID_SOCIAL_SECURITY_NUMBER';
	
	public function validate(){
		if(!preg_match('/^[\d]{3}-[\d]{2}-[\d]{4}$/',$this->getData())){
			$this->throwException(self::INVALID_SOCIAL_SECURITY_NUMBER);
		}
		return $this;
	}
}