<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidCreditCard extends ValidString{
	
	const INVALID_CARD_NUMBER = 'INVALID_CARD_NUMBER';
	
	public function validate(){
		$number = $this->getData();
		
		// Strip any non-digits (useful for credit card numbers with spaces and hyphens)
		$number=preg_replace('/\D/', '', $number);
	
		// Set the string length and parity
		$number_length=mb_strlen($number);
		$parity=$number_length % 2;
	
		// Loop through each digit and do the maths
		$total=0;
		for ($i=0; $i<$number_length; $i++) {
			$digit=$number[$i];
			// Multiply alternate digits by two
			if ($i % 2 == $parity) {
				$digit*=2;
				// If the sum is two digits, add them together (in effect)
				if ($digit > 9) {
					$digit-=9;
				}
			}
			// Total up the digits
			$total+=$digit;
		}
	
		// If the total mod 10 equals 0, the number is valid
		if( ($total % 10 == 0) == false ){
			$this->throwException(self::INVALID_CARD_NUMBER);
		}
		return $this;
	}
}