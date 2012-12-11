<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ExampleComponentPage extends PageController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
		
	}
	
	protected $_AuthenticationComponent;
	protected $_AuthenticationComponentController;
	
	protected function handleRequest(){
		
		$this->_AuthenticationComponent = $this->getComponents()->Authentication($this);
		if($this->_AuthenticationComponent->checkRequest(AuthenticationComponent::REQUEST_LOGIN)){
			$this->_AuthenticationComponentController = $this->_AuthenticationComponent->getComponentController(AuthenticationComponent::REQUEST_LOGIN);
		}
		
	}
	
	public function renderJSON(){ return $this->_output = $this->_AuthenticationComponentController->renderJSON(); }
	public function renderXML(){ return $this->_output = $this->_AuthenticationComponentController->renderXML(); }
	public function renderHTML(){ return $this->_page_body = $this->_AuthenticationComponentController->renderHTML(); }
	
}
