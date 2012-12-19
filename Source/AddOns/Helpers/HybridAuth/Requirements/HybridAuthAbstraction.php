<?php 

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

/*
 * Because this is a native abstraction, this data probably can/should be returned as DataObjects/DataCollections typed for this helper
 */

class HybridAuthAbstraction{
	
	private 
		$_config,
		$_hybridAuth,
		$_safe_redirect_url,
		$_logout_url,
		$_login_url;
	
	public function __construct($config, $login_url='/HybridAuth/Login/', $logout_url='/HybridAuth/Logout/', $safe_redirect_url='/'){
		$this->_config = $config;
		$this->_login_url = $login_url;
		$this->_logout_url = $logout_url;
		$this->_safe_redirect_url = $safe_redirect_url;
		$this->_hybridAuth = new Hybrid_Auth( $config );
	}
	
	public function getHybridAuthInstance(){
		return $this->_hybridAuth;
	}
	
	public function getAdapter($provider){
		return $this->_hybridAuth->getAdapter($provider);
	}
	
	public function getSerializedCredentialsByProvider($requested_provider){
				
		$session_data = $this->getHybridAuthInstance()->getSessionData();
		
// 		pr($_SESSION['HA::STORE']);
		$session_array = unserialize($session_data);
// 		pr($session_array);
		
		$keys_by_provider = array();
		$full_keys_by_provider = array();
		$providers = array();
		if(is_array($session_array)){
			foreach($session_array as $key => $value){
				$key_parts = explode('.',$key);
				$container = array_shift($key_parts);
				$provider = array_shift($key_parts);
				$simple_key = implode('.',$key_parts);
				$keys_by_provider[$provider][$simple_key] = $value;
				$full_keys_by_provider[$provider][$container.'.'.$provider.'.'.$simple_key] = $value;
			}
//  		pr('Keys by provider');
// 		pr($keys_by_provider);
//  		pr($full_keys_by_provider);
// 		pr('Segmented credentials');
// 		foreach($keys_by_provider as $provider => $credentials){
// 			pr($provider);
// 			pr($credentials);
// 			pr(serialize($credentials));
// 		}
//  		pr('All credentials');
			
			//$credentials = serialize($keys_by_provider);
			return serialize($full_keys_by_provider[lower($requested_provider)]);
		} else {
			// throw error here?
			// return false?
			return serialize(array());
		}
	}
	
	public function setProviderCredentials(Array $provider_credentials){
		$this->_hybridAuth->restoreSessionData(serialize($provider_credentials));
	}
	
	public function getConnectedStates(){
		$connected_states = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$connected_states[$connected_provider] = $this->getAdapter($connected_provider)->isUserConnected();
		}
		return $connected_states;
	}
	
	public function getConnectedAdapters(){
		$connected_adapters = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$connected_adapters[$connected_provider] = $this->getAdapter($connected_provider);
		}
		return $connected_adapters;
	}
	
	public function endpoint(){
		require_once( "Hybrid/Endpoint.php" );
		
		Hybrid_Endpoint::process();
	}
	
	public function logoutAllProviders(){
		return $this->_hybridAuth->logoutAllProviders();
	}
	
	public function logoutProvider($provider){
		return $this->_hybridAuth->getAdapter($provider)->logout();
	}
	
	public function setConnectedStatuses($status){
		$results = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$Adapter = $this->_hybridAuth->getAdapter($connected_provider);
			$results[$connected_provider] = $Adapter->setUserStatus($status);
// 			if(method_exists($Adapter, 'setUserStatus')){
// 			} else {
// 				$results[$connected_provider] = array();
// 			}
		}
		return $results;
	}
	
	public function getAvailableProviders(){
		$providers = $this->_hybridAuth->getProviders();
		return count($providers)?$providers:array();
	}
	
	public function getConnectedProviders(){
		$connected_providers = $this->_hybridAuth->getConnectedProviders();
		return count($connected_providers)?$connected_providers:array();
	}
	
	public function getConnectedProfiles(){
		$connected_profiles = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$connected_profiles[$connected_provider] = $this->getConnectedProfileByProvider($connected_provider);
		}
		return $connected_profiles;
	}
	
	public function getConnectedAPIs(){
		$connected_apis = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$connected_apis[$connected_provider] = $this->_hybridAuth->getAdapter($connected_provider)->api();
		}
		return $connected_apis;
	}
	
	public function getConnectedTimelines(){
		$connected_timelines = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$Adapter = $this->_hybridAuth->getAdapter($connected_provider);
			$connected_timelines[$connected_provider] = $Adapter->getUserActivity('timeline');
// 			if(method_exists($Adapter, 'getUserActivity')){
// 			} else {
// 				$connected_timelines[$connected_provider] = array();
// 			}
		}
		return $connected_timelines;
	}
	
	public function getConnectedContacts(){
		$connected_contacts = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$Adapter = $this->_hybridAuth->getAdapter($connected_provider);
// 			pr($Adapter);
// 			pr(get_class_methods($Adapter));
			$connected_contacts[$connected_provider] = $Adapter->getUserContacts();
// 			if(method_exists($Adapter, 'getUserContacts')){
// 			} else {
// 				$connected_contacts[$connected_provider] = array();
// 			}
		}		
		return $connected_contacts;
	}
	
	public function getConnectedActivity(){
		$connected_activity = array();
		foreach( $this->_hybridAuth->getConnectedProviders() as $connected_provider){
			$Adapter = $this->_hybridAuth->getAdapter($connected_provider);
			$connected_activity[$connected_provider] = $Adapter->getUserActivity('me'); // 
// 			if(method_exists($Adapter, 'getUserActivity')){
// 			} else {
// 				$connected_activity[$connected_provider] = array();
// 			}
		}
		return $connected_activity;
	}
	
	private function handleException($e, $adapter=null){
		switch( $e->getCode() ){
			case 0 : 
				throw new HybridAuthException("Unspecified error.", HybridAuthException::UNSPECIFIED_ERROR, $e);
			case 1 : 
				throw new HybridAuthException("Hybriauth configuration error.", HybridAuthException::CONFIGURATION_ERROR, $e);
			case 2 : 
				throw new HybridAuthException("Provider not properly configured.", HybridAuthException::BAD_PROVIDER_CONFIG, $e);
			case 3 : 
				throw new HybridAuthException("Unknown or disabled provider.", HybridAuthException::PROVIDER_DISABLED, $e);
			case 4 : 
				throw new HybridAuthException("Missing provider application credentials.", HybridAuthException::PROVIDER_CREDENTIALS_MISSING, $e);
			case 5 : 
				throw new HybridAuthException("Authentification failed. '
					.'The user has canceled the authentication or the provider refused the connection.", HybridAuthException::AUTHENTICATION_FAILED, $e);
			case 6 : 
				if($adapter){ $adapter->logout(); }
				throw new HybridAuthException("User profile request failed. '
					.'Most likely the user is not connected to the provider and he should to authenticate again.", HybridAuthException::PROFILE_REQUEST_FAILED, $e);
			case 7 : 
				if($adapter){ $adapter->logout(); }
				throw new HybridAuthException("User not connected to the provider.", HybridAuthException::USER_NOT_CONNECTED, $e);
		}		
	}
	
	public function getConnectedProfileByProvider($provider){
		try{
			// check if the user is currently connected to the selected provider
			if( !$this->_hybridAuth->isConnectedWith( $provider ) ){ 
				// redirect him back to login page
				header('Location: '.$this->_login_url.'?error='.$provider);
			}
	
			// call back the requested provider adapter instance (no need to use authenticate() as we already did on login page)
			$Adapter = $this->_hybridAuth->getAdapter($provider);
	
			// grab the user profile
			$user_data = $Adapter->getUserProfile();
			return $user_data;
	    } catch( Exception $e ){
			ob_flush();
			self::handleException($e);
		}
	}
	
	public function authenticate($provider, $redirect_url=null){
	 	ob_start();
		
		try{
 			
			// try to authenticate the selected $provider
			$adapter = $this->_hybridAuth->authenticate( $provider );

			// if okay, we will redirect to user profile page
			if(!is_null($redirect_url)){ $this->_hybridAuth->redirect( $redirect_url ); }
			
	 		$contents = ob_get_contents();
	 		ob_flush();
			return $contents;
			
	    } catch( Exception $e ){
			ob_flush();
			self::handleException($e,$adapter);
		}
		
	}
	
}