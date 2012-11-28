<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/



class IndexPage extends PageController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
		
	}
	
	protected function handleRequest(){
		// business logic here
		
		$social_sessions = unserialize('a:2:{s:10:"HA::CONFIG";a:3:{s:14:"php_session_id";s:34:"s:26:"5bg7n3r3blfbqh133t69dfe284";";s:7:"version";s:16:"s:9:"2.1.0-dev";";s:6:"config";s:1536:"a:8:{s:10:"debug_mode";s:0:"";s:10:"debug_file";s:0:"";s:8:"base_url";s:45:"http://minnow.badpxl.com/HybridAuth/Endpoint/";s:9:"providers";a:10:{s:6:"OpenID";a:1:{s:7:"enabled";s:1:"1";}s:5:"Yahoo";a:2:{s:7:"enabled";s:0:"";s:4:"keys";a:2:{s:3:"key";s:0:"";s:6:"secret";s:0:"";}}s:3:"AOL";a:1:{s:7:"enabled";s:1:"1";}s:6:"Google";a:2:{s:7:"enabled";s:0:"";s:4:"keys";a:2:{s:2:"id";s:0:"";s:6:"secret";s:0:"";}}s:8:"Facebook";a:2:{s:7:"enabled";s:1:"1";s:4:"keys";a:2:{s:2:"id";s:15:"461820303860356";s:6:"secret";s:32:"fd6a75169c964c926835d3609b9d761b";}}s:7:"Twitter";a:2:{s:7:"enabled";s:1:"1";s:4:"keys";a:2:{s:3:"key";s:22:"xwsjQ8mp4RZpRWDJaNE0YA";s:6:"secret";s:42:"EznZlkdYQoRB8iuCleD6HmhNmyVJvfVMpzD9jLfxL4";}}s:4:"Live";a:2:{s:7:"enabled";s:0:"";s:4:"keys";a:2:{s:2:"id";s:0:"";s:6:"secret";s:0:"";}}s:7:"MySpace";a:2:{s:7:"enabled";s:0:"";s:4:"keys";a:2:{s:3:"key";s:0:"";s:6:"secret";s:0:"";}}s:8:"LinkedIn";a:2:{s:7:"enabled";s:0:"";s:4:"keys";a:2:{s:3:"key";s:0:"";s:6:"secret";s:0:"";}}s:10:"FourSquare";a:2:{s:7:"enabled";s:0:"";s:4:"keys";a:2:{s:2:"id";s:0:"";s:6:"secret";s:0:"";}}}s:9:"path_base";s:73:"/var/www/badpxl.com/Source/AddOns/Helpers/HybridAuth/Requirements/Hybrid/";s:14:"path_libraries";s:84:"/var/www/badpxl.com/Source/AddOns/Helpers/HybridAuth/Requirements/Hybrid/thirdparty/";s:14:"path_resources";s:83:"/var/www/badpxl.com/Source/AddOns/Helpers/HybridAuth/Requirements/Hybrid/resources/";s:14:"path_providers";s:83:"/var/www/badpxl.com/Source/AddOns/Helpers/HybridAuth/Requirements/Hybrid/Providers/";}";}s:9:"HA::STORE";a:5:{s:40:"hauth_session.twitter.token.access_token";s:58:"s:50:"969488239-N4wL46qjjXKMLCF4uQJStt2sq345fr8Km0eVimSC";";s:47:"hauth_session.twitter.token.access_token_secret";s:49:"s:41:"TXRa6a9fqDiHbLh3dhfOEN8tQ2M3odPvkv75a3rJU";";s:34:"hauth_session.twitter.is_logged_in";s:4:"i:1;";s:35:"hauth_session.facebook.is_logged_in";s:4:"i:1;";s:41:"hauth_session.facebook.token.access_token";s:119:"s:110:"AAAGkBespYoQBAPhvQTMJeNvpFvfYpiWc3w7syP6R1jRctXJuaUuyfEw99Cp7aZAalXYsKICERoutZAhrlK364TNiZADXyk4gX8FrdAtEQZDZD";";}}');
		
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
	}
	
	public function renderJSON(){ parent::renderJSON(); }
	public function renderXML(){ parent::renderXML(); }
	public function renderHTML(){ parent::renderHTML(); }
	
}

// class CookieData{
	
// 	public 
// 		$token, 
// 		$data,
// 		$name;
// }