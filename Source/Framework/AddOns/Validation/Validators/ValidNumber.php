<?php 

class ValidNumber extends ValidationRule{
	
	const INVALID_NUMBER_FORMAT = 'INVALID_NUMBER_FORMAT';
	const INVALID_NUMBER_OVER_MAX = 'INVALID_NUMBER_OVER_MAX';
	const INVALID_NUMBER_UNDER_MIN = 'INVALID_NUMBER_UNDER_MIN';
	
	public function __construct($data){
		parent::__construct($data);
		
		if( !(mb_strlen($this->getData()) === 0 || ctype_digit((string) $this->getData())) ){
			$this->throwException(self::INVALID_NUMBER_FORMAT);
		}
	}
	
	public function max($limit){
		if( $this->getData() >= $limit ){
			$this->throwException(self::INVALID_NUMBER_OVER_MAX, $limit);
		}
		return $this;
	}
	
	public function min($limit){
		if( $this->getData() <= $limit ){
			$this->throwException(self::INVALID_NUMBER_UNDER_MIN, $limit);
		}
		return $this;
	}
	
	public function under($limit){
		if( $this->getData() < $limit ){ // just less than 
			$this->throwException(self::INVALID_NUMBER_OVER_MAX, $limit);
		}
		return $this;
	}
	
	public function over($limit){
		if( $this->getData() > $limit ){ // just greater than
			$this->throwException(self::INVALID_NUMBER_UNDER_MIN, $limit);
		}
		return $this;
	}
	
}