<?php 

class ValidWords extends ValidString{
	
	const INVALID_WORD_COUNT_OVER_LIMIT = 'INVALID_WORD_COUNT_OVER_LIMIT';
	const INVALID_WORD_COUNT_UNDER_LIMIT = 'INVALID_WORD_COUNT_UNDER_LIMIT';
	const INVALID_WORD_FORMAT = 'INVALID_WORD_FORMAT';
	
	// Arguments for the utf8 \p{...} selector
	// C	Other	 
	// Cc	Control	 
	// Cf	Format	 
	// Cn	Unassigned	 
	// Co	Private use	 
	// Cs	Surrogate	 
	// L	Letter	 Includes the following properties: Ll, Lm, Lo, Lt and Lu.
	// Ll	Lower case letter	 
	// Lm	Modifier letter	 
	// Lo	Other letter	 
	// Lt	Title case letter	 
	// Lu	Upper case letter	 
	// M	Mark	 
	// Mc	Spacing mark	 
	// Me	Enclosing mark	 
	// Mn	Non-spacing mark	 
	// N	Number	 
	// Nd	Decimal number	 
	// Nl	Letter number	 
	// No	Other number	 
	// P	Punctuation	 
	// Pc	Connector punctuation	 
	// Pd	Dash punctuation	 
	// Pe	Close punctuation	 
	// Pf	Final punctuation	 
	// Pi	Initial punctuation	 
	// Po	Other punctuation	 
	// Ps	Open punctuation	 
	// S	Symbol	 
	// Sc	Currency symbol	 
	// Sk	Modifier symbol	 
	// Sm	Mathematical symbol	 
	// So	Other symbol	 
	// Z	Separator	 
	// Zl	Line separator	 
	// Zp	Paragraph separator	 
	// Zs	Space separator	 
	
	// EX: 
	// \p{L} <-- utf8 word characters
	// \p{P} <-- punctuation
	// \p{Zs} <-- space separator
	// \s <-- space
	// \d <-- digit
	// \P{L}
	
	public function allowSimpleWordsOnly($allow_line_breaks=false, $allow_dashes=false){
		$allowances = '';
		if($allow_dashes){ $allowances = '\-'; }
		if($allow_line_breaks){ $allowances = '\n\r'; }
		
		if(preg_match('/[^A-z\s'.$allowances.']+/s',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		return $this;
	}
	
	public function allowSimpleWordCharactersAndNumbers($allow_line_breaks=false, $allow_dashes=false){
		$allowances = '';
		if($allow_dashes){ $allowances = '\-'; }
		if($allow_line_breaks){ $allowances = '\n\r'; }
				
		if(preg_match('/[^A-z\d\s'.$allowances.']+/s',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		
		return $this;
	}

	public function allowSimpleWordCharactersAndPunctuation($allow_line_breaks=false){
		$allowances = '';
		if($allow_line_breaks){ $allowances = '\n\r'; }
				
		if(preg_match('/[^A-z\p{Po}\s'.$allowances.']+/s',  $this->getData())){ // \p{Po} = simple symbols and punctuation like .,&/\'" etc...
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		
		return $this;
	}

	public function allowSimpleWordCharactersPunctuationAndNumbers($allow_line_breaks=false){
		$allowances = '';
		if($allow_line_breaks){ $allowances = '\n\r'; }
				
		if(preg_match('/[^A-z\d\p{Po}\s'.$allowances.']+/s',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		
		return $this;
	}
	
	public function allowUTF8WordsOnly($allow_line_breaks=false, $allow_dashes=false){
		$allowances = '';
		if($allow_dashes){ $allowances = '\p{Pd}'; }
		if($allow_line_breaks){ $allowances = '\n\r\p{Zp}\p{Zl}'; }
		
		if(preg_match('/[^\p{L}\p{M}\p{Zs}'.$allowances.']+/us',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		return $this;
	}
	
	public function allowUTF8WordsAndNumbersAndPunctuation($allow_line_breaks=false){
		$allowances = '';
		if($allow_line_breaks){ $allowances = ''; }
		
		if(preg_match('/[^\p{L}\p{M}\p{P}\p{N}\s'.$allowances.']+/us',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		return $this;
	}
	
	public function allowUTF8WordsAndNumbers($allow_line_breaks=false, $allow_dashes=false){
		$allowances = '';
		if($allow_dashes){ $allowances = '\p{Pd}'; }
		if($allow_line_breaks){ $allowances = '\n\r\p{Zp}\p{Zl}'; }
		
		if(preg_match('/[^\p{L}\p{M}\p{N}\s'.$allowances.']+/us',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		return $this;
	}
	
	public function allowUTF8WordsAndPunctuation($allow_line_breaks=false){
		$allowances = '';
		if($allow_line_breaks){ $allowances = '\n\r\p{Zp}\p{Zl}'; }
		
		if(preg_match('/[^\p{L}\p{M}\p{P}\s'.$allowances.']+/us',  $this->getData())){
			$this->throwException(self::INVALID_WORD_FORMAT);
		}
		return $this;
	}
	
	public function min($limit){
		$string = preg_replace('/\s+/us', ' ', $this->getData());
		$pieces = explode(' ',$string);
		if(count($pieces) < $limit){
			$this->throwException(self::INVALID_WORD_COUNT_UNDER_LIMIT);
		}
		return $this;
	}
	
	public function max($limit=null){
		$string = preg_replace('/\s+/us', ' ', $this->getData());
				
		$pieces = explode(' ',$string);
		if(count($pieces) > $limit){
			$this->throwException(self::INVALID_WORD_COUNT_OVER_LIMIT);
		}
		return $this;
	}
	
}