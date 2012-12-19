<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class WelcomeComponentController extends ComponentController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		// Here you can place your welcome page logic. 
		// Does the account require further registration steps? 
		// Is the account a certain role type that would require a different welcome view?
		// .. etc 
	}
	
	public function renderJSON(){ return parent::renderJSON(); }
	public function renderXML(){ return parent::renderXML(); }
	public function renderHTML(){
// 		$PageController = PageController::cast($this->getParentComponent()->getParentController());
// 		$PageController->addCss('Libraries/Zocial/zocial');
// 		$PageController->addCss('Components/Authentication/Pages/Login');
// 		return parent::renderHTML();
		
		$ID = $this->getParentComponent()->identifyUser();
		
		return pr($ID,1);
	}
	
}
