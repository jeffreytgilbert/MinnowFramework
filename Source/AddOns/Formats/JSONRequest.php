<?php

abstract class JSONRequest extends Controller{
	public function __construct(){
		parent::__construct();
		$this->loadIncludedFiles();
		$this->handleRequest();
	}
	
	abstract public function renderJSON();
	
	public function renderStatusMessagesAsJSON(){
		return json_encode(array(
			'Notices'=>$this->Notices->toArray(),
			'Errors'=>$this->Errors->toArray(),
			'Confirmations'=>$this->Confirmations->toArray(),
			'Messages'=>$this->Messages->toArray() // depricated
		));
	}
}

