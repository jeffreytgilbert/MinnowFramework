<?php

class ResendEmailComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		$this->loadActions(array('Email/EmailActions'));
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected function handleRequest(){
		global $ID, $UserId;
		if(!$ID->isOnline()) { $this->redirect('/Account/Login',200,true); }
		
		if(isset($_POST['resend_email'])){
			$this->_data['sent'] = true;
			EmailActions::sendEmailValidationRequest($ID->getData('email'), $ID->get('user_id'), $ID->getData('login_name'));
		}
	}
	
	public function renderPage(){
		if(isset($this->_data['sent'])){
			$this->_page_body = $this->runCodeReturnOutput('pages/Account/ResendEmail/email_resent.php');
		} else {
			$this->_page_body = $this->runCodeReturnOutput('pages/Account/ResendEmail/layout.php');
		}
	}
}

