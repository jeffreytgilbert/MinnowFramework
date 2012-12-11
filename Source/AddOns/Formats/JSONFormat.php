<?php

interface JSONCapable{
	public function renderJSON();
}

trait JSONFormat{
	
	public function renderJSON(){
		$this->_output = $this->renderStatusMessagesAsJSON();
		return $this->_output;
	}
	
	public function renderStatusMessagesAsJSON(){
		return json_encode(array(
			'Notices'=>$this->Notices->toArray(),
			'Errors'=>$this->Errors->toArray(),
			'Confirmations'=>$this->Confirmations->toArray(),
			'Messages'=>$this->Messages->toArray() // depricated
		));
	}
}

