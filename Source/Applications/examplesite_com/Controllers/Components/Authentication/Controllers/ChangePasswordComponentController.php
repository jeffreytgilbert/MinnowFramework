<?php

class ChangePasswordComponentController extends ComponentController{
	protected function loadIncludedFiles(){
		//$this->loadModels(array('ChangePassword'));
		$this->loadActions(array('Account/AccountActions'));
		/* page dependencies */
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		/* business logic */
		
		$ID = $this->getParentComponent()->identifyUser();
		
		if($ID->isOnline()){
			$MyID = RuntimeInfo::instance()->idAsMember();
		}
		
		if(!$ID->isOnline()){ 
			$this->Errors->set('403','You do not have permission to perform this task.'); 
			return; 
		}

		if(isset($_POST['Password']) && is_array($_POST['Password'])){
			$this->Input = new DataObject($_POST['Password']);
			
			if($this->Input->getData('current_password','e') != $MyID->get('password')){
				$this->Errors->set('BAD_PASSWORD','The password you entered does not match the one we have on file for you.');
			}
			
			if(!$this->Input->getData('new_password','Is::password')){
				$this->Errors->set('BAD_FORMAT','Your password must be 6 to 50 characters long, '
													.'and can only contain standard letters, numbers, spaces, dashes, and underscores.');
			}
			
			if($this->Input->get('new_password') != $this->Input->get('confirm_password')){
				$this->Errors->set('CONFIRM_PASSWORD','The passwords you provided do not match. Please reconfirm your new password.');
			}
			
			if($this->Errors->length() == 0){
				AccountActions::changeMyPassword($this->Input->get('new_password'));
				
				$ID->set('password',$this->Input->get('new_password'));
				
				$UserID = RuntimeInfo::instance()->userSession();
				$UserID->refreshIDSession();
				
				$this->Confirmations->set('Success','Your password has been updated.');
			}
			
		} else if(isset($_GET['framework']['output_format']) && $_GET['framework']['output_format'] == 'JSON') {
			$this->Errors->set('no_data','No settings data specified.');
		}
	}
	
}