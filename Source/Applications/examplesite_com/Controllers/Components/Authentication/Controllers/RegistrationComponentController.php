<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// Prompt user to login via social plugins (if supported) and form

class RegistrationComponentController extends ComponentController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected $_Authentication;
	
	public function handleRequest(){
		
		$this->_Authentication = $this->getParentComponent();
		
		$ID = $this->_Authentication->identifyUser();
		
		// check to see if the person is logged in or identified
		if($ID->isOnline()){
			// if they're logged in already, send them to the welcome page
			$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url')); // have the welcome page handle further registration steps. This is the social sign in forwarding page.
		}
		
		$PageController = PageController::cast($this->getParentComponent()->getParentController());
		
		// Get session helper
		$SessionHelper = $PageController->getHelpers()->Session();
		// Get cookie helper
		$CookieHelper = $PageController->getHelpers()->SecureCookie();
		
		// Assign the form data to a form handler
		$Form = $this->getForm('Registration');
		
		// Check for form login from local page request
		// If data exists in expected form, check it as a login against the db
		if($Form->hasBeenSubmitted()){
			
			// Handle errors one at a time (if you want to handle them all at the same time, throw each one in a try catch block of its own
			
			try{
				$Form->checkPassword('password')->required()->strong();
				$Form->checkEmail('unique_identifier')->required()->validate();
			} catch(Exception $e){
				$errors = $Form->getCurrentErrors();
			}
			
			if(count($errors) == 0){
				$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
			} else {
				foreach($errors as $field => $error){
					$this->getErrors()->set($field, key($error));
				}
			}
		}
		
		$HybridAuth = $this->getHelpers()->HybridAuth();
		$this->getDataObject()->set('providers', array_keys($HybridAuth->getAvailableProviders()));
		
	}
	
	public function renderJSON(){ return parent::renderJSON(); }
	public function renderXML(){ return parent::renderXML(); }
	public function renderHTML(){
		$PageController = PageController::cast($this->getParentComponent()->getParentController());
		$PageController->addCss('Libraries/Zocial/zocial');
		$PageController->addCss('Components/Authentication/Pages/Registration');
		$PageController->addJs('Libraries/jQuery.Validate/jquery.validate');
		$PageController->addJs('Components/Authentication/Pages/Registration');
//		$PageController->addJs('Libraries/jQuery.Validate/additional-methods');
		return parent::renderHTML();
	}
	
}
