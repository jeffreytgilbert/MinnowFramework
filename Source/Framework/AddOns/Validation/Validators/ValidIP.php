<?php 

class ValidIP extends ValidString{
	
	const INVALID_IP_ADDRESS = 'INVALID_IP_ADDRESS';
	
	public function simpleCheck(){
		if(!filter_var($this->getData(), FILTER_VALIDATE_IP)){
			$this->throwException(self::INVALID_IP_ADDRESS);
		}
		return $this;
	}
	
	public function v4(){
		if(!filter_var($this->getData(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)){
			$this->throwException(self::INVALID_IP_ADDRESS);
		}
		return $this;
	}
	
	public function publicIpV4(){
		if(!filter_var($this->getData(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE)){
			$this->throwException(self::INVALID_IP_ADDRESS);
		}
		return $this;
	}
	
	public function v6(){
		if(!filter_var($this->getData(), FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)){
			$this->throwException(self::INVALID_IP_ADDRESS);
		}
		return $this;
	}
	
	public function validate(){
		if(!filter_var($this->getData(), FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)){
			$this->throwException(self::INVALID_IP_ADDRESS);
		}
		return $this;
	}
}