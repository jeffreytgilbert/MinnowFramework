<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class LoginPage extends PageController implements HTMLCapable{
	protected function loadIncludedFiles(){
	}
	
	protected function handleRequest(){
		$this->_page_body = $this->getHelpers()->HybridAuth()->authenticate('Facebook');
	}

	public function renderHTML(){ $this->_output = $this->_page_body; }
	
}