<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class VerifyComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		$this->loadModels(array('EmailValidation'));
		$this->loadActions(array('EmailValidationActions'));
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		$ID = $this->getParentComponent()->identifyUser();
		
		if(!$ID->isOnline()){
			$this->flashError('login_required', 'You must be logged in with the account that owns the email address you\'re attempting to validate.');
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
		}
		
		if(isset($_GET['code'])) {
			if(EmailValidationActions::validateEmail($_GET['code'], $ID->getInteger('user_id'))){
				$this->flashConfirmation('email_validated', 'Your email address has been validated.');
				$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
			} else {
				$this->flashError('missing_info', 'The confirmation code was missing or invalid and this contact has not been validated.');
				$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
			}
		} else {
			$this->flashError('missing_info', 'The confirmation code was missing and this contact has not been validated.');
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
		}
	}
	
}
