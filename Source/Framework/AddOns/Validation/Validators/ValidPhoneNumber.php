<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidPhoneNumber extends ValidString{
	
	const INVALID_US_PHONE_NUMBER = 'INVALID_US_PHONE_NUMBER';
	const INVALID_UK_PHONE_NUMBER = 'INVALID_UK_PHONE_NUMBER';
	
	public function validateUSNumber(){
		if( preg_match('/^[+]?([0-9]?)[(|s|-|.]?([0-9]{3})[)|s|-|.]*([0-9]{3})[s|-|.]*([0-9]{4})$/', $this->getData()) ) {
			$this->throwException(self::INVALID_US_PHONE_NUMBER);
		}
		return $this;
	}
	
	public function validate7DigitNumber(){
		$numbersOnly = ereg_replace("[^0-9]", '', $this->getData());
		$numberOfDigits = mb_strlen($numbersOnly);
		if( !($numberOfDigits == 7) ) {
			$this->throwException(self::INVALID_US_PHONE_NUMBER);
		}
		return $this;
	}
	
	public function validateUKNumber(){
		$phone = $this->getData();
		if (mb_strlen($phone) > 0) {
			$phone = str_replace (' ', '', $phone);
			$phone = str_replace ('-', '', $phone);
			$phone = str_replace ('(', '', $phone);
			$phone = str_replace (')', '', $phone);
			$phone = str_replace ('[', '', $phone);
			$phone = str_replace (']', '', $phone);
			$phone = str_replace ('{', '', $phone);
			$phone = str_replace ('}', '', $phone);
		
			if (preg_match('/^(\+)[\s]*(.*)$/',$phone)) {
				$this->throwException(self::INVALID_US_PHONE_NUMBER,'UK telephone number without the country code, please');
			}
			
			if (!preg_match('/^[0-9]{10,11}$/',$phone)) {
				$this->throwException(self::INVALID_US_PHONE_NUMBER,'UK telephone numbers should contain 10 or 11 digits');
			}
			
			if (!preg_match('/^0[0-9]{9,10}$/',$phone)) {
				$this->throwException(self::INVALID_US_PHONE_NUMBER,'The telephone number should start with a 0');
			}
		}
		return $this;
	}
	
}