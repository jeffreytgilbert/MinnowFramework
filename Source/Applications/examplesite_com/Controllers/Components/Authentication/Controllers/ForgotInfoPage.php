<?php

class ForgotInfoComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		$this->loadActions(array('Email/EmailActions'));
		$this->loadActions(array('Account/PasswordResetActions'));
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected function handleRequest(){
		global $ID;
	//	$RuntimeInfo = RuntimeInfo::instance();
		
		if(isset($_POST['send'])){
			$Reset = new Model($_POST['send']);
			$this->Input = $Reset;
			if($Reset->getData('email','Is::set') || $Reset->getData('login_name','Is::set'))
			{
				if($Reset->getData('email','Is::set')) { $Credentials = PasswordResetActions::setPassCodeByEmail($Reset->get('email')); }
				else { $Credentials = PasswordResetActions::setPassCodeByUserName($Reset->get('login_name')); }
				if($Credentials)
				{
					EmailActions::sendPasswordResetRequest($Credentials->get('email'), $Credentials->get('user_id'), $Credentials->get('login_name'), $Credentials->get('pass_code'));
					$this->Notices->set('Notice','An email containing your alias and pass code to reset your password has been sent '
											.'to the email address on the account. Please check your email to continue.');
					$this->_data['notice_sent'] = true;
				}
				else { $this->Errors->set('ALIAS_NOT_FOUND','Sorry, but that alias could not be found in our database. Please try again.'); }
			}
		}
		
		if(isset($_POST['reset'])) {
			$Reset = new Model($_POST['reset']);
			$this->Input = $Reset;
		} else if(isset($_GET['pass_code']) && isset($_GET['email'])){
			$Reset = new Model(array('pass_code'=>$_GET['pass_code'], 'email'=>$_GET['email']));
			$this->Input = $Reset;
		} else {
			$Reset = new Model();
			$this->Input = $Reset;
		}
		
		if($Reset->length() == 0) { return; }
		
		if($Reset->getData('pass_code','Is::set') && $Reset->getData('email','Is::set') && $Reset->getData('password','Is::set'))
		{
			if($ID->isOnline()) { IdentifyUser::logout(); }
			
			$Credentials = PasswordResetActions::getPassCodeByEmailAddress($Reset->get('email'));
			if($Credentials->get('pass_code') == $Reset->get('pass_code'))
			{
				if(Is::password($Reset->get('password')))
				{
					PasswordResetActions::updatePassword($Credentials->get('user_id'), $Reset->get('password'));
					PasswordResetActions::setPassCodeByEmail($Credentials->get('email')); // once it's been used, reset it to something random but don't send an email with the new code
					$this->redirect('/Account/Login/?msg=9',200,true);
				}
				else	{ $this->Errors->set('PASSWORD_NOT_VALID','Please choose a password of between 6-50 alphanumeric characters.'); }
			}
			else		{ $this->Errors->set('CREDENTIALS_NOT_VALID','Please check that both the <b>email</b> and <b>pass code</b> are correct. '); }
		} else if($Reset->getData('submit','Is::set')){
			$this->Errors->set('PASSWORD_NOT_VALID','Please choose a password of between 6-50 alphanumeric characters.');
		}
	
	}
	
	public function renderPage(){
//		$RuntimeInfo = RuntimeInfo::instance();
		if($this->Input->getData('pass_code','Is::set') || isset($this->_data['notice_sent'])){
			
			$this->_data = array_merge(array(
				'pass_code' => $this->Input->getData('pass_code'),
				'email' => $this->Input->getData('email'),
				'password' => $this->Input->getData('password'),
				'password_label' => ($this->Errors->length() == 0)?'Up to 50 characters':'<em class="error">'.implode('<br>',$this->Errors->toArray()).'</em>',
				'pass_code_label' => 'This code expires once it\'s been used',
				'email_label' => 'This is the email address to which the pass code was addressed'
			),$this->_data);
			
			$this->_page_body = $this->runCodeReturnOutput('pages/Account/ForgotInfo/reset_password.php');
		} else {
			$this->_data = array_merge(array(
				'send_name_label' => 'Up to 20 letters or numbers',
				'send_email_label' => 'An email will be sent to this address, which must be checked, before your account will be unlocked'
			),$this->_data);
			
			$this->_page_body = $this->runCodeReturnOutput('pages/Account/ForgotInfo/forgot_info.php');
		}
	}
}
