<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidURL extends ValidString{
	
	const INVALID_URL_CHECKED_WITH_CURL = 'INVALID_URL_CHECKED_WITH_CURL';
	const INVALID_URL_CHECKED_WITH_FOPEN = 'INVALID_URL_CHECKED_WITH_FOPEN';
	const INVALID_URL_FORMAT = 'INVALID_URL_FORMAT';
	
	public function validateAgainstCurl(){
		$c=curl_init();
		curl_setopt($c,CURLOPT_URL,$this->getData());
		curl_setopt($c,CURLOPT_HEADER,1);//get the header
		curl_setopt($c,CURLOPT_NOBODY,1);//and *only* get the header
		curl_setopt($c,CURLOPT_RETURNTRANSFER,1);//get the response as a string from curl_exec(), rather than echoing it
		curl_setopt($c,CURLOPT_FRESH_CONNECT,1);//don't use a cached version of the url
		if(!@curl_exec($c)){
			$this->throwException(self::INVALID_URL_CHECKED_WITH_CURL);
		}
		return $this;
	}
	
	public function validateAgainstFopen(){
		if(!@fopen($this->getData(),'r')){
			$this->throwException(self::INVALID_URL_CHECKED_WITH_FOPEN);
		}
		return $this;
	}
	
	public function validate(){
		if(filter_var($this->getData(), FILTER_VALIDATE_URL)){
			$this->throwException(self::INVALID_URL_FORMAT);
		}
		return $this;
	}
	
}