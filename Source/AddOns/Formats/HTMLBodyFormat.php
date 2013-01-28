<?php

interface HTMLBodyCapable{
	public function renderHTMLBody();
}

trait HTMLBodyFormat{
	
	public function initializeHTMLBody(){
// 		if($this instanceof PageController){
// 			$Page = PageController::cast($this);
// 		} else {
// 			$Page = ComponentController::cast($this);
// 		}
	}
	
	public function renderHTMLBody(){
		if($this instanceof PageController){
			$Page = PageController::cast($this);
			$path = ($Page->getControllerPath() == '')?$Page->getControllerName():$Page->getControllerPath().'/'.$Page->getControllerName();
			$path = 'Pages/'.$path.'/layout.php';
			$this->_output = $this->_page_body = $this->runCodeReturnOutput($path);
		} else {
			$Page = ComponentController::cast($this);
			$path = Path::toComponents().$this->_component_name.'/Views/'.$this->_component_controller_name.'/layout.php';
			$this->_output = $this->_page_body = $this->runCodeReturnOutput($path,false);
		}
		
		return $this->_page_body;
	}
	
}
