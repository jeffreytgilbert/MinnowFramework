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
		// this is a little annoying... there's no cast method on controller. but if there were i'd need to change controller cast methods everywhere from easy to harder code. need return type hinting PHP!
		if($this instanceof PageController){
			$Page = PageController::cast($this);
		} else {
			$Page = ComponentController::cast($this);
		}
		
		return json_encode(array(
			'Notices'=>$Page->getNotices()->toArrayRecursive(),
			'Errors'=>$Page->getErrors()->toArrayRecursive(),
			'Confirmations'=>$Page->getConfirmations()->toArrayRecursive(),
			'Data'=>$Page->getDataObject()->toArrayRecursive()
		));
	}
}

