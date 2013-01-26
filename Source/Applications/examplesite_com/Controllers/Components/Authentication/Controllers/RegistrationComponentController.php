<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// Prompt user to login via social plugins (if supported) and form

class RegistrationComponentController extends ComponentController{
	protected function loadIncludedFiles(){
		$this->loadModels(array('EmailValidation'));
		$this->loadActions(array('EmailValidationActions','EmailActions'));
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
			$errors = array();
			
			try{
				$Form->checkEmail('unique_identifier')->required()->maxLength(320)->validate();
				
 				$Form->checkWords('first_name')->required()->allowUTF8WordsOnly(false,true)->minLength(2)->maxLength(30);
				$Form->checkWords('last_name')->required()->allowUTF8WordsOnly(false,true)->minLength(2)->maxLength(30);
				
				$Form->checkPassword('password')->required()->strong();
			} catch(Exception $e){
				$errors = $Form->getCurrentErrors();
			}
			
			if(count($errors) == 0){
				
				$UserLogin = UserLoginActions::selectByUniqueIdentifierAndProviderTypeId($Form->getFieldData('unique_identifier'), 1);
				
				if($UserLogin->getInteger('user_id') > 0){
					$PageController->getErrors()->set('unique_identifier','Sorry, but an account is already registered with this email address.');
					$HybridAuth = $this->getHelpers()->HybridAuth();
					$this->getDataObject()->set('providers', array_keys($HybridAuth->getAvailableProviders()));
					return;
				}
				
				// create a user account for subsequent logins
				
				$UserAccount = new UserAccount(array(
					'first_name'=>$Form->getFieldData('first_name'),
					'last_name'=>$Form->getFieldData('last_name'),
					'latitude'=>$ID->getLocationFromIp()->getNumber('latitude'),
					'longitude'=>$ID->getLocationFromIp()->getNumber('longitude'),
					'gmt_offset'=>$ID->getLocationFromIp()->getNumber('gmt_offset'),
					'password_hash'=>$this->getHelpers()->SecureHash()->generateSecureHash($Form->getFieldData('password'))
				));
				
				// Create a user account to link user logins and other bits of info to
				$user_id = UserAccountActions::insertUserAccountFromForm($UserAccount);
				
				UserLoginActions::insertUserLogin(new UserLogin(array(
					'user_id'=>$user_id,
					'unique_identifier'=>$Form->getFieldData('unique_identifier'),
					'user_login_provider_id'=>1 // provider type id for emails
				)));
				
				EmailActions::sendEmailValidationRequest($Form->getFieldData('unique_identifier'), $user_id, $Form->getFieldData('first_name'), $Form->getFieldData('last_name'));
				
				try{
					$ID = $this->_Authentication->authenticateForm($Form->getFormDataAsDataObject());
					
					if($ID instanceof OnlineMember){
						$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
					} else {
					//	$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
					}
				} catch(AuthenticationException $e){
					pr('Was there an auth error?');
					// At whatever point, developer can decide how they'd like the login to handle these exceptions. This is convenience code.
					switch($e->getCode()){
						case AuthenticationException::BAD_CREDENTIALS:
						case AuthenticationException::TOO_MANY_BAD_REQUESTS:
						case AuthenticationException::USER_ACCOUNT_NOT_REGISTERED:
						case AuthenticationException::USER_BAN:
						default:
//							$this->getErrors()->set($e->getCode(),$e->getMessage()); // how to set an error in the component controller
							$PageController->getErrors()->set($e->getCode(),$e->getMessage()); // how to set an error in the controller calling this component controller
							break;
					}
				}
			} else {
				foreach($errors as $field => $error){
					if(key($error) != ''){
						$PageController->getErrors()->set($field,$field.': '.key($error)); // how to set an error in the controller calling this component controller
					}
				}
			}
		}
		
		$HybridAuth = $this->getHelpers()->HybridAuth();
		$this->getDataObject()->set('providers', array_keys($HybridAuth->getAvailableProviders()));
		
	}
	
	public function renderHTML(){
		$PageController = PageController::cast($this->getParentComponent()->getParentController());
		$PageController->addCss('Libraries/Zocial/zocial');
		$PageController->addCss('Components/Authentication/Pages/Registration');
		$PageController->addJs('Libraries/jQuery.Validate/jquery.validate');
		$PageController->addJs('Libraries/jQuery.Validate/additional-methods');
		$PageController->addJs('Components/Authentication/Pages/Registration');
//		$PageController->addJs('Libraries/jQuery.Validate/additional-methods');
		return parent::renderHTML();
	}
	
}
