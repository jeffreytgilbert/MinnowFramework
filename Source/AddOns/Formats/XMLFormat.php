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
		// this is a little annoying... there's no cast method on controller. but if there were i'd need to change controller cast methods everywhere from easy to harder code. need return type hinting PHP!
		if($this instanceof PageController){
			$Page = PageController::cast($this);
		} else {
			$Page = ComponentController::cast($this);
		}
		
		$array = array(
			'Notices'=>$Page->getNotices()->toArrayRecursive(),
			'Errors'=>$Page->getErrors()->toArrayRecursive(),
			'Confirmations'=>$Page->getConfirmations()->toArrayRecursive(),
			'Data'=>$Page->getData()->toArrayRecursive()
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