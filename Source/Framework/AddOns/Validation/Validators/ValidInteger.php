<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidInteger extends ValidNumber{
	
	const INVALID_INTEGER_FORMAT = 'INVALID_INTEGER_FORMAT';
	
	public function validate(){
		if($this->getData() != (int)$this->getData()){
			$this->throwException(self::INVALID_INTEGER_FORMAT);
		}
		return $this;
	}
}