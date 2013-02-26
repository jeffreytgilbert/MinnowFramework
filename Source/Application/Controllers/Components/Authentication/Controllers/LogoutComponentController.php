<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class LogoutComponentController extends ComponentController{
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		
		$ID = $this->getParentComponent()->identifyUser();
		if($ID instanceof OnlineGuest){ $this->redirect('/'); }
		
		// Set for form object
		$Form = $this->_Input->getObject('Logout');
		
		// Check for form login from local page request
		if($Form->length() > 0){
			
			// If data exists in expected form, check it as a login against the db
			if(trim($Form->getString('exit')) == 'true'){
				$this->getParentComponent()->logout();
				$this->redirect('/');
			}
		}
	}
	
}
