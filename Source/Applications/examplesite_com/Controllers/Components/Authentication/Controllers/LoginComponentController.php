<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/



////////////////
// This page is for handling the users request to login from a social signon button press or from a form submission with login credentials (like username and pass)
// This page isn't for checking to see if the user is logged in. It's for logging them in. To see if a user is logged in, call AuthenticationComponent::identifyMe();
////////////////




class LoginComponentController extends ComponentController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected $_Authentication;
	
	public function handleRequest(){
		
		$this->_Authentication = $this->getParentComponent();
		
		$ID = $this->_Authentication->identifyUser();
		
		// check to see if the person is logged in or identified
		if($ID->isOnline()){
			// if they're logged in already, send them to the welcome page
			$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url')); // have the welcome page handle further registration steps. This is the social sign in forwarding page.
			
		// This page requires a provider to be provided to function as a social sign on page. Check for that provider
		}
		
		$PageController = PageController::cast($this->getParentComponent()->getParentController());
		
		// Get session helper
		$SessionHelper = $PageController->getHelpers()->Session();
		// Get cookie helper
		$CookieHelper = $PageController->getHelpers()->SecureCookie();
		
		// Set for form object
		$Form = $this->_Input->getObject('Login');
		
		// Check for form login from local page request
		if($Form->length() > 0){
			// If data exists in expected form, check it as a login against the db
			if(trim($Form->getString('signin')) != ''){
				// If login request is legit, log out anyone currently logged in, and then login user from result set
				if(1){
					// query results from db for user data cache
					// log person out if logged in
					// log person in with data from db by saving data to the: 
					// Session
					// SecureCookie
					// Db as a user_session for auditing and logging people out (links to php session id and also cookie token so either can be canceled)
					
				} else {
					// if request is bad, set error messages 
					$this->getErrors()->set('ErrorCode',''); // how to set an error in the component controller
					$PageController->getErrors()->set('ErrorCode',''); // how to set an error in the controller calling this component controller
				}
			}
		}
		
		$HybridAuth = $this->getHelpers()->HybridAuth();
		$this->getDataObject()->set('providers', array_keys($HybridAuth->getAvailableProviders()));
		
		
		// social sign in credentials for testing logins
		
 		$SecureCookieHelper = $this->getHelpers()->SecureCookie();
		
		$cookie_data = array(
			'user_id'=>1,
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'expiration_date'=>new DateTime('+2 week'),
			'ip_address'=>$this->getHelpers()->Location()->guessIP(),
			'token'=>'1234'
		);
		
		// store minnow framework user login
		$SecureCookieHelper->store('MFUL', serialize($cookie_data));

		// so this bit will mimic what is going to come from the database
//  		$session_data = unserialize($this->getHelpers()->HybridAuth()->getCredentialsByProvider('Twitter')) + 
//  						unserialize($this->getHelpers()->HybridAuth()->getCredentialsByProvider('Facebook'));
// 		pr($session_data);

		// now logout all providers to simulate a log out condition
//		$this->getHelpers()->HybridAuth()->logoutAllProviders();
		
		// now try to set the session data in hybrid auth by the data fields captured in step 1
//		$this->getHelpers()->HybridAuth()->setProviderCredentials($session_data); // this method can run 1 individual session, or all sessions
		
//		$this->getAuthentication()->authenticateFromHybridAuth($session_data);
//  		pr($_SESSION);
// 		pr($this->getHelpers()->HybridAuth()->getConnectedActivity());

// class CookieData{
	
// 	public 
// 		$token, 
// 		$data,
// 		$name;
// }
	}
	
	public function renderJSON(){ return parent::renderJSON(); }
	public function renderXML(){ return parent::renderXML(); }
	public function renderHTML(){
		$PageController = PageController::cast($this->getParentComponent()->getParentController());
		$PageController->addCss('Libraries/Zocial/zocial');
		$PageController->addCss('Components/Authentication/Pages/Login');
		return parent::renderHTML();
	}
	
}
