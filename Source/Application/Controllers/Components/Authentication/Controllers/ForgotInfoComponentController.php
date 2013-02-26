<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class ForgotInfoComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		$this->loadModels(array('UserPasswordResetRequest'));
		$this->loadActions(array('EmailActions','UserPasswordResetRequestActions'));
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		$ID = $this->getParentComponent()->identifyUser();
		
		$RequestForm = $this->getForm('Request');
		
		if($RequestForm->isSubmitted()){
			try{
				$RequestForm->checkEmail('email')->required()->validate();
				
				// See if the email address is taken, and if not, send nothing
				$UserLogin = UserLoginActions::selectByUniqueIdentifierAndProviderTypeId($RequestForm->getFieldData('email'), 1);
				
				if($UserLogin->getString('unique_identifier') != ''){
					$reset_code = UserPasswordResetRequestActions::insertUserPasswordResetRequest($UserLogin->getInteger('user_id'));
					EmailActions::sendPasswordResetRequest($UserLogin->getString('unique_identifier'), $UserLogin->getInteger('user_id'), $UserLogin->getString('unique_identifier'), $reset_code);
					$this->flashNotice('Notice','An email containing your alias and pass code to reset your password has been sent. Please check your email to continue.');
					return;
				} else {
					$this->flashError('AliasNotFound','Sorry, but that alias could not be found in our database. Please try again.');
					return;
				}
			}catch(Exception $e){
				$errors = $RequestForm->getCurrentErrors();
				
				foreach($errors as $field => $error){
					if(key($error) != ''){
						$this->flashError($field,$field.': '.key($error));
					}
				}
				
				return;
			}
		}
		
		
		if(isset($_GET['reset_code'])) {
			// submit data into the input array
			$this->setInput('Reset',array(
				'reset_code'=>htmlentities(strip_tags($_GET['reset_code']))
			));
		}
		
		$ResetForm = $this->getForm('Reset');
		
		if($ResetForm->isSubmitted()){
			try{
				$ResetForm->checkWords('reset_code')->required()->length(19)->allowSimpleWordCharactersAndNumbers(false,true); // 4 characters, 4 blocks, 3 dashes
				$ResetForm->checkPassword('password')->required()->strong()->validate();
				
				// Get the code's match in the db if there is one.
				$UserPasswordResetRequest = UserPasswordResetRequestActions::selectByResetCode($ResetForm->getFieldData('reset_code'));
				
				// See if the user actually did request it
				if($UserPasswordResetRequest->getInteger('user_id') > 0){
					// Clear the request if everything is valid
					UserPasswordResetRequestActions::deleteUserPasswordResetRequestById($UserPasswordResetRequest->getInteger('user_id'));
					
					// Set a new password for this person
					UserAccountActions::setUserPassword(new UserAccount(array(
						'user_id'=>$UserPasswordResetRequest->getInteger('user_id'),
						'password_hash'=>RuntimeInfo::instance()->getHelpers()->SecureHash()->generateSecureHash($ResetForm->getFieldData('password'))
					)));
					
					$this->flashConfirmation('Success','Your password has been reset to the one you requested. Try logging in to test it!');
					
					$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
					return;
				} else {
					$this->flashError('CodeNotFound','Sorry, but no reset request was found on record. Please send a new one as it may have expired.');
					return;
				}
				
			} catch(Exception $e){
				$errors = $ResetForm->getCurrentErrors();
				
				foreach($errors as $field => $error){
					if(key($error) != ''){
						$this->flashError($field,$field.': '.key($error));
					}
				}
				return;
			}
		}
	
	}
	
	public function renderHTML(){
		$PageController = PageController::cast($this->getParentComponent()->getParentController());
		$PageController->addCss('Components/Authentication/Pages/ForgotInfo');
 		$PageController->addJs('Components/Authentication/Pages/ForgotInfo');
		
		if($this->getInput('Reset')->getString('reset_code') != ''){
			$this->_page_body = $this->runCodeReturnOutput(Path::toComponents().$this->_component_name.'/Views/ForgotInfo/reset_layout.php', false);
		} else {
			$this->_page_body = $this->runCodeReturnOutput(Path::toComponents().$this->_component_name.'/Views/ForgotInfo/request_layout.php', false);
		}
				
		return $this->_page_body;
	}
	
}
