<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidSocialSecurityNumber extends ValidString{
	
	const INVALID_SOCIAL_SECURITY_NUMBER = 'INVALID_SOCIAL_SECURITY_NUMBER';
	
	public function validate(){
		if(!preg_match('/^[\d]{3}-[\d]{2}-[\d]{4}$/',$this->getData())){
			$this->throwException(self::INVALID_SOCIAL_SECURITY_NUMBER);
		}
		return $this;
	}
}