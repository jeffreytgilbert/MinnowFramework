<?php

class ResendConfirmationEmailComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		$this->loadModels(array('EmailValidation'));
		$this->loadActions(array('EmailValidationActions','EmailActions'));
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		$ID = $this->getParentComponent()->identifyUser();
		
		if(!$ID->isOnline()) {
			$this->flashError('403','You do not have permission to perform this task.');
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
			return; 
		}
		
		$Form = $this->getForm('ResendConfirmationEmail');
		
		if($Form->isSubmitted()){
			try{
				$Form->checkPassword('password')->required()->strong()->validate();
				
				if( !$this->getHelpers()->SecureHash()->validatePassword($Form->getFieldData('password'), $ID->getUserAccount()->getString('password_hash'))){
					$this->flashError('BadPassword','The password you entered does not match the one we currently have on record.');
					return;
				}
				
				$UserLoginCollection = UserLoginActions::selectListByUserId($ID->getInteger('user_id'));
				$UserLogin = $UserLoginCollection->getUserLoginByFieldValue('user_login_provider_id', 1);
				
				if($UserLogin->getString('unique_identifier') != ''){
					EmailActions::sendEmailValidationRequest(
						$UserLogin->getString('unique_identifier'), 
						$ID->getInteger('user_id'), 
						$ID->getUserAccount()->getString('first_name'),
						$ID->getUserAccount()->getString('last_name')
					);
					$this->flashNotice('Notice','A new confirmation email has been sent to your account to validate this email address.');
					return;
				} else {
					$this->flashError('EmailNotFound','No email was found in our system for your account.');
					return;
				}
				
			}catch(Exception $e){
				$errors = $Form->getCurrentErrors();
				
				foreach($errors as $field => $error){
					if(key($error) != ''){
						$this->flashError($field,$field.': '.key($error));
					}
				}
			}
		}
	}
}

