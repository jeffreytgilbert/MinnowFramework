<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class LoginComponentController extends ComponentController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
	}
	
	public function handleRequest(){
		
		// social sign in credentials for testing logins
		
		
// 		pr($social_sessions);
		$_SESSION['HA::CONFIG'] = $social_sessions['HA::CONFIG'];
		$_SESSION['HA::STORE'] = $social_sessions['HA::STORE'];
		
// 		pr($_SESSION);
		
		$SecureCookieHelper = $this->getHelpers()->SecureCookie();
		
		$cookie_data = array(
			'user_id'=>1,
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'expiration_date'=>new DateTime('+2 week'),
			'ip_address'=>$this->getHelpers()->Location()->guessIP(),
			'token'=>'1234'
		);
		
		$SecureCookieHelper->store('MFUL', serialize($cookie_data));
		
		
// 		$CookieData = new CookieData();
// 		$CookieData->data = serialize(array('1'=>'2','3'=>'4'));
// 		$CookieData->name = 'sample_data';
// 		$CookieData->token = '1234';
		
// 		$SecureCookieHelper->store(
// 			'login', 
// 			serialize($CookieData)
// 		);

//		pr($_COOKIE);
		
// 		$RetrievedCookieData = $SecureCookieHelper->fetch('login');
		
// 		$RetrievedCookieData = unserialize($RetrievedCookieData);
		
// 		pr($RetrievedCookieData);
		
//		$SecureCookieHelper->delete('login');
		
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
	public function renderHTML(){ return parent::renderHTML(); }
	
}
