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
				
				if( !$this->getHelpers()->SecureHash()->validatePassword($Form->getFieldData('password'), $ID->getUserAccount()->getString('password_hash'))){
					$this->flashError('BadPassword','The password you entered does not match the one we currently have on record.');
					return;
				}
				
				$this->flashConfirmation('Confirmation','Your account has been closed and any associated records placed offline. To reopen your account, simply log back in.');
				
				UserAccountActions::closeAccount($ID->getInteger('user_id'));
				
				// Log out through auth component
				$this->getParentComponent()->logout();
				
				// Send em back to the login screen
				$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
				
				// If one were so inclined, one could surely author a condolences letter to the late account
				// like so:
// 				$UserLoginCollection = UserLoginActions::selectListByUserId($ID->getInteger('user_id'));
// 				$UserLogin = $UserLoginCollection->getUserLoginByFieldValue('user_login_provider_id', 1);
// 				if($UserLogin->getString('unique_identifier') != ''){
// 					EmailActions::sendCloseAccountNotification(
// 						$UserLogin->getString('unique_identifier'), 
// 						$ID->getInteger('user_id'), 
// 						$ID->getUserAccount()->getString('first_name'),
// 						$ID->getUserAccount()->getString('last_name')
// 					);
// 					return;
// 				}
				
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