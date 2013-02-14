<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ValidString extends ValidationRule{
	
	// needs ends with and starts with
	
	const INVALID_STRING_MAX_LENGTH = 'INVALID_STRING_MAX_LENGTH';
	const INVALID_STRING_MIN_LENGTH = 'INVALID_STRING_MIN_LENGTH';
	const INVALID_STRING_EXACT_LENGTH = 'INVALID_STRING_EXACT_LENGTH';
	const INVALID_STRING_BAD_MATCH = 'INVALID_STRING_BAD_MATCH';
	const INVALID_STRING_BAD_OPTION = 'INVALID_STRING_BAD_OPTION';
	const INVALID_STRING_CONTAINS_NO_MATCH = 'INVALID_STRING_CONTAINS_NO_MATCH';
	const INVALID_STRING_CONTAINS_BAD_MATCH = 'INVALID_STRING_CONTAINS_BAD_MATCH';
	
	public function minLength($limit){
		if( mb_strlen($this->getData()) <= $limit ){ 
			$this->throwException(self::INVALID_STRING_MIN_LENGTH, $limit);
		}
		return $this;
	}
	
	public function maxLength($limit){
		if( mb_strlen($this->getData()) >= $limit ){ 
			$this->throwException(self::INVALID_STRING_MAX_LENGTH, $limit);
		}
		return $this;
	}
	
	public function length($characters){
		if( mb_strlen($this->getData()) != $characters ){
			$this->throwException(self::INVALID_STRING_EXACT_LENGTH, $characters);
		}
		return $this;
	}
	
	public function mustContainOneOfThese(Array $strings, $case_sensitive=false){
		foreach($strings as $string){
			$this->mustContain($string, $case_sensitive);
		}
		return $this;
	}
	
	public function mustNotContainOneOfThese(Array $strings, $case_sensitive=false){
		foreach($strings as $string){
			$this->mustNotContain($string, $case_sensitive);
		}
		return $this;
	}
	
	public function allowSimpleAlphabetCharactersOnly($allow_dashes=false, $allow_underscores=false){
		$allowances = '';
		if($allow_dashes){ $allowances = '-'; }
		if($allow_underscores){ $allowances = '_'; }
		
		if(preg_match('/[^A-z\s'.$allowances.']+/s',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		return $this;
	}

	public function allowUTF8AlphabetCharactersOnly($allow_dashes=false, $allow_underscores=false){
		$allowances = '';
		if($allow_dashes){ $allowances = '-'; }
		if($allow_underscores){ $allowances = '_'; }
		
		if(preg_match('/[^\p{L}\p{M}\p{Zs}'.$allowances.']+/us',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		return $this;
	}
	
	public function mustContain($string, $case_sensitive=false){
		if(!$case_sensitive){
			if( !stristr($this->getData(), $string) ){
				$this->throwException(self::INVALID_STRING_CONTAINS_NO_MATCH, $string);
			}
		} else {
			if( !strstr($this->getData(), $string) ){
				$this->throwException(self::INVALID_STRING_CONTAINS_NO_MATCH, $string);
			}
		}
		return $this;
	}
	
	public function mustNotContain($string, $case_sensitive=false){
		if(!$case_sensitive){
			if( stristr($this->getData(), $string) ){
				$this->throwException(self::INVALID_STRING_CONTAINS_BAD_MATCH, $string);
			}
		} else {
			if( strstr($this->getData(), $string) ){
				$this->throwException(self::INVALID_STRING_CONTAINS_BAD_MATCH, $string);
			}
		}
		return $this;
	}
		
}