<?php 

class ValidInteger extends ValidNumber{
	
	const INVALID_INTEGER_FORMAT = 'INVALID_INTEGER_FORMAT';
	
	public function validate(){
		if($this->getData() != (int)$this->getData()){
			$this->throwException(self::INVALID_INTEGER_FORMAT);
		}
		return $this;
	}
}