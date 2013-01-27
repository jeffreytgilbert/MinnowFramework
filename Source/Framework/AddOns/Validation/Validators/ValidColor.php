<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidColor extends ValidationRule{
	
	const INVALID_COLOR_HEX_VALUE = 'INVALID_COLOR_HEX_VALUE';
	
	public function hex(){
		if(!preg_match('/^#(?:(?:[a-f0-9]{3}){1,2})$/i',$this->getData())){
			$this->throwException(self::INVALID_COLOR_HEX_VALUE);
		}
		return $this;
	}
	
}