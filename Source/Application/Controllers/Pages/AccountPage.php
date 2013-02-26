<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class AccountPage extends PageController implements HTMLCapable, JSONCapable, XMLCapable, HTMLBodyCapable{

	public function handleRequest(){
		// business logic here
		
		// tell authentication component to handle account related requests. 
		$this->_AuthenticationComponentController = $this->getComponents()->Authentication($this)->mapRequest();
		
	}
	
	public function renderJSON(){ return $this->_output = $this->_AuthenticationComponentController->renderJSON(); }
	public function renderXML(){ return $this->_output = $this->_AuthenticationComponentController->renderXML(); }
	public function renderHTML(){ return $this->_page_body = $this->_AuthenticationComponentController->renderHTML(); }
	public function renderHTMLBody(){ return $this->_output = $this->_AuthenticationComponentController->renderHTMLBody(); }
	
}
