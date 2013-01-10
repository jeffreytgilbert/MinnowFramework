<?php

class ParentSignUpPage extends TemplatedPageRequest implements IJSONRequest{
	
	protected function loadIncludedFiles(){
		$this->loadModels(array(
			'Role'
		));
		$this->loadActions(array(
			'Email/EmailActions',
			'Account/RegistrationActions',
			'Account/AccountActions',
			'Account/IpBanActions',
			'RoleActions'
		));
	}
	
	protected function handleRequest(){
		global $ID, $UserId;
		
		if($ID->isOnline()) { 
//			$this->redirect('/Account/Welcome',200,true); 
			$this->Notices->set('Ok', 'Already logged in.');
			return;
		}
		
		$ip_list = array();
		if($ID->getIp())
		{
			$ip_fragments = explode('.',$ID->getIp());
			if(count($ip_fragments) == 4){
				$ip_list[] = $ip_fragments[0].'.'.$ip_fragments[1];
				$ip_list[] = $ip_fragments[0].'.'.$ip_fragments[1].'.'.$ip_fragments[2];
				$ip_list[] = $ID->getIp();
			}
		}
		if($ID->getProxy())
		{
			$proxy_fragments = explode('.',$ID->getProxy()); 
			if(count($proxy_fragments) == 4){
				$ip_list[] = $proxy1 = $proxy_fragments[0].'.'.$proxy_fragments[1];
				$ip_list[] = $proxy2 = $proxy_fragments[0].'.'.$proxy_fragments[1].'.'.$proxy_fragments[2];
				$ip_list[] = $ID->getProxy();
			}
		}
		
		if($ID->getIp() != '::1'){
			if(count($ip_list) > 0){
				if(IpBanActions::isBanned($ip_list)) {
					$this->Errors->set(401, 'Banned.');
					return;
				}
			}
			if(count($ip_list) == 0) {
			$this->Errors->set(401, 'Banned.');
				return;
			}
		}
		
		if(isset($_POST['registration']))
		{
			$input_error=array();
			$this->Input = new User($_POST['registration']);
			
			if(!isset($_POST['registration']['login_name'])) { $this->Errors->set('login_name','Please enter your user name.'); }
			else { $_POST['login']['l']=$_POST['registration']['login_name']; } //so that the login history works
			
			if(!$this->Input->getData('login_name','Is::username'))
			{ $this->Errors->set('login_name','Your Full Name can only consist of letters, numbers, and spaces and must be 6 to 20 characters long.'); }
			
			if(!$this->Input->getData('password','Is::password')){
				$this->Errors->set('password', 'Your password must be 6 to 50 characters long, '
													.'and can only contain standard letters, numbers, spaces, dashes, and underscores.');
			}
			
			if(!$this->Input->getData('email','Is::email'))
			{
				$this->Errors->set('email', 'The email you entered is invalid. '
												.'Please enter a valid email address for logins, '
												.'password retreivals, and system notifications.');
			}
			
			if( $this->Errors->length() < 1 && 
				!AccountActions::validName($this->Input->get('login_name'))) { 
				$this->Errors->set('login_name', 'This user name is already in use.'); 
			}
			
			if($this->Errors->length()<1)
			{
				// This returns either a user id OR an array of errors.
				$user_id = RegistrationActions::insertParent($this->Input);
				
				// Check to see if its the array of errors first.
				if(is_array($user_id))
				{
					foreach($user_id as $error_type => $message){
						$this->Errors->set($error_type,$message);
					}
					unset($user_id);
					return;
				}
				else
				{
					unset($input_error);
					$this->InputId = $UserId;
					
//					$this->redirect('/Account/Login/',200,true);
					$this->Confirmations->set(200, 'Account created.');
					return;
				}
			} 
		}
		else
		{
			if(isset($_GET['registration'])) { $test=$_GET['registration']; }
			else { $test = array(); }
			
			$this->Input = new DataObject($test);
		}
	}
	
	public function renderStatusMessagesAsJSON(){
		return json_encode(array(
			'Notices'=>$this->Notices->toArray(),
			'Errors'=>$this->Errors->toArray(),
			'Confirmations'=>$this->Confirmations->toArray(),
			'Messages'=>$this->Messages->toArray() // depricated
		));
	}
	
	public function renderJSON(){
		/* page logic */
		$this->_output = $this->renderStatusMessagesAsJSON();
	}
	
	public function renderPage(){
		
		if($this->Confirmations->getData('Success','Is::set')){
			$this->redirect('/Account/Login/',200,true);
		} else if($this->Notices->getData('Ok','Is::set')){
			$this->redirect('/Account/Welcome/',200,true);
		} else if($this->Errors->getData(401,'Is::set')){
			$this->redirect(ERROR_PAGE_404,404,true);
		}
		
		$this->addCss('pages/Account/ParentSignUp');
		$this->addJs('pages/Account/ParentSignUp');
				
		/* page logic */
		
		$this->_page_body = $this->runCodeReturnOutput('pages/Account/ParentSignUp/layout.php');
	}
}

		

