<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// Prompt user to login via social plugins (if supported) and form

class LoginComponentController extends ComponentController implements HTMLCapable, JSONCapable, XMLCapable{
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
		
		// Set for form object
		$Form = $this->getInput()->getObject('Login');
		
		// Check for form login from local page request
		// If data exists in expected form, check it as a login against the db
		if($Form->length() > 0 && 
			trim($Form->getString('unique_identifier')) != '' && 
			$Form->getString('password') != ''){

			try{
				$ID = $this->_Authentication->authenticateForm($Form);
				if($ID instanceof OnlineMember){
					$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
				}
			} catch(AuthenticationException $e){
				// At whatever point, developer can decide how they'd like the login to handle these exceptions. This is convenience code.
				switch($e->getCode()){
					case AuthenticationException::BAD_CREDENTIALS:
					case AuthenticationException::TOO_MANY_BAD_REQUESTS:
					case AuthenticationException::USER_ACCOUNT_NOT_REGISTERED:
					case AuthenticationException::USER_BAN:
					default:
						$this->flashError($e->getCode(),$e->getMessage());
						break;
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
		$PageController->addCss('Components/Authentication/Pages/Login');
//		$PageController->addJs('Libraries/jQuery.Validate/jquery.validate');
		$PageController->addJs('Components/Authentication/Pages/Login');
//		$PageController->addJs('Libraries/jQuery.Validate/additional-methods');
		return parent::renderHTML();
	}
	
}
