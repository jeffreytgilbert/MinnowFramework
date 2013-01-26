<?php

class ResendEmailComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		$this->loadActions(array('Email/EmailActions'));
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		$ID = $this->getParentComponent()->identifyUser();
		
		if(!$ID->isOnline()) { $this->redirect($this->getParentComponent()->getConfig()->get('login_page_url')); }
		
		if(isset($_POST['resend_email'])){
			$this->_data['sent'] = true;
			EmailActions::sendEmailValidationRequest($ID->getString('email'), $ID->getInteger('user_id'), $ID->getString('login_name'));
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

