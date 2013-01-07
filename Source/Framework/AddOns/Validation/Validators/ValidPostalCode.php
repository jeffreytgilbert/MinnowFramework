<?php 

class ValidPostalCode extends ValidString{
	
	const INVALID_POSTAL_CODE_FORMAT = 'INVALID_POSTAL_CODE_FORMAT';
	
	public function UnitedStates(){
		if(!preg_match("/^\d{5}([\-]?\d{4})?$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function UnitedKingdom(){
		if(!preg_match("/^(GIR|[A-Z]\d[A-Z\d]??|[A-Z]{2}\d[A-Z\d]??)[ ]??(\d[A-Z]{2})$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Germany(){
		if(!preg_match("/\b((?:0[1-46-9]\d{3})|(?:[1-357-9]\d{4})|(?:[4][0-24-9]\d{3})|(?:[6][013-9]\d{3}))\b/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Canada(){
		if(!preg_match("/^([ABCEGHJKLMNPRSTVXY]\d[ABCEGHJKLMNPRSTVWXYZ])\ {0,1}(\d[ABCEGHJKLMNPRSTVWXYZ]\d)$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function France(){
		if(!preg_match("/^(F-)?((2[A|B])|[0-9]{2})[0-9]{3}$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Italy(){
		if(!preg_match("/^(V-|I-)?[0-9]{5}$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Australia(){
		if(!preg_match("/^(0[289][0-9]{2})|([1345689][0-9]{3})|(2[0-8][0-9]{2})|(290[0-9])|(291[0-4])|(7[0-4][0-9]{2})|(7[8-9][0-9]{2})$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Netherlands(){
		if(!preg_match("/^[1-9][0-9]{3}\s?([a-zA-Z]{2})?$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Spain(){
		if(!preg_match("/^([1-9]{2}|[0-9][1-9]|[1-9][0-9])[0-9]{3}$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Denmark(){
		if(!preg_match("/^([D-d][K-k])?( |-)?[1-9]{1}[0-9]{3}$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Sweden(){
		if(!preg_match("/^(s-|S-){0,1}[0-9]{3}\s?[0-9]{2}$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
	public function Belgium(){
		if(!preg_match("/^[1-9]{1}[0-9]{3}$/i",$this->getData())){
			$this->throwException(self::INVALID_POSTAL_CODE_FORMAT);
		}
		return $this;
	}
	
}