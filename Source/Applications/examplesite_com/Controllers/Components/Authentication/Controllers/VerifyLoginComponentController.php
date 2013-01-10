<?php

class VerifyLoginComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		$this->loadActions(array('EmailValidationActions'));
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		if(isset($_GET['code'])) {
			if(EmailValidationActions::validateEmail($_GET['code'])){
				$ID = $this->getParentComponent()->identifyUser();
				if($ID->isOnline()){
					$this->flashConfirmation('email_validated', 'Your email address has been validated.');
					$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
				} else {
					$this->flashConfirmation('email_validated', 'Your email address has been validated.');
					// to autologin this person or to not auto login this person, that is the question. Err on the side of security.
					$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
				}
			} else {
				$this->flashError('missing_info', 'The confirmation code was missing or invalid and this contact has not been validated.');
				$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
			}
		} else {
			$this->flashError('missing_info', 'The confirmation code was missing and this contact has not been validated.');
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
		}
	}

// 	public function renderJSON(){ return parent::renderJSON(); }
// 	public function renderXML(){ return parent::renderXML(); }
// 	public function renderHTML(){ return parent::renderHTML(); }
	
}
