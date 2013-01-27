<?php

class CloseAccountComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		// Ex: $this->loadModels(array('Close'));
		// Ex: $this->loadActions(array('CloseActions'));
		/* page dependencies */
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
			$ID = $this->getParentComponent()->identifyUser();
		
		if(!$ID->isOnline()) {
			$this->flashError('403','You do not have permission to perform this task.');
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
			return; 
		}
		
		$Form = $this->getForm('CloseAccount');
		
		if($Form->isSubmitted()){
			try{
				$Form->checkPassword('password')->required()->strong()->validate();
				
				if( !$this->getHelpers()->SecureHash()->validatePassword($Form->getFieldData('current_password'), $ID->getUserAccount()->getString('password_hash'))){
					$this->flashError('BadPassword','The password you entered does not match the one we currently have on record.');
					return;
				}
				
				// Update the database with the new hash
				$new_password_hash = $this->getHelpers()->SecureHash()->generateSecureHash($Form->getFieldData('new_password'));
				UserAccountActions::setUserPassword(new UserAccount(array(
					'user_id'=>$ID->getInteger('user_id'),
					'password_hash'=>$new_password_hash
				)));
				
				// Update the session with the new hash
				$ID->getUserAccount()->set('password_hash', $new_password_hash);
				$this->getParentComponent()->updateOnlineMemberSession($ID);
				
				$this->flashConfirmation('Success','Your password has been updated.');
				
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