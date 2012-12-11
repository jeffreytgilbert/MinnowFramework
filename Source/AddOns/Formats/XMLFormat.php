<?php

interface XMLCapable{
	public function renderXML();
}

trait XMLFormat{
	
	public function renderXML(){
		header("Content-type: text/xml; charset=utf-8");
		$this->_output = $this->renderStatusMessagesAsXML();
		return $this->_output;
	}
	
	public function renderStatusMessagesAsXML(){
		$array = array(
			'Notices'=>$this->Notices->toArray(),
			'Errors'=>$this->Errors->toArray(),
			'Confirmations'=>$this->Confirmations->toArray(),
			'Messages'=>$this->Messages->toArray() // depricated
		);
		
		$xml = new SimpleXMLElement("<?xml version=\"1.0\"?><response></response>");
		
		// function call to convert array to xml
		self::array_to_xml($array,$xml);
		
		return $xml->asXML();
	}
	
	public function array_to_xml($array, &$xml) {
		foreach($array as $key => $value) {
			if(is_array($value)) {
				if(!is_numeric($key)){
					$subnode = $xml->addChild($key);
					self::array_to_xml($value, $subnode);
				}
				else{
					self::array_to_xml($value, $xml);
				}
			}
			else {
				$xml->addChild("$key","$value");
			}
		}
	}
}