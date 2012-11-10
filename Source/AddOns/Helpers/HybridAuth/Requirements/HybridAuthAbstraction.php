<?php 

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
	    }
		catch( Exception $e ){  
			// In case we have errors 6 or 7, then we have to use Hybrid_Provider_Adapter::logout() to 
			// let hybridauth forget all about the user so we can try to authenticate again.
	
			// Display the recived error, 
			// to know more please refer to Exceptions handling section on the userguide
			switch( $e->getCode() ){ 
				case 0 : echo "Unspecified error."; break;
				case 1 : echo "Hybriauth configuration error."; break;
				case 2 : echo "Provider not properly configured."; break;
				case 3 : echo "Unknown or disabled provider."; break;
				case 4 : echo "Missing provider application credentials."; break;
				case 5 : echo "Authentification failed. " 
						  . "The user has canceled the authentication or the provider refused the connection."; 
				case 6 : echo "User profile request failed. Most likely the user is not connected "
						  . "to the provider and he should to authenticate again."; 
					   $Adapter->logout(); 
					   break;
				case 7 : echo "User not connected to the provider."; 
					   $Adapter->logout(); 
					   break;
			} 
	
			echo "<br /><br /><b>Original error message:</b> " . $e->getMessage();
			echo "<hr /><h3>Trace</h3> <pre>" . $e->getTraceAsString() . "</pre>";  
		}
	}
	
	public function authenticate($provider){
		ob_start();
		try{
				
			// try to authenticate the selected $provider
			$adapter = $this->_hybridAuth->authenticate( $provider );

			// if okay, we will redirect to user profile page
			$this->_hybridAuth->redirect( $this->_safe_redirect_url );
			
		} catch( Exception $e ){
		// In case we have errors 6 or 7, then we have to use Hybrid_Provider_Adapter::logout() to
		// let hybridauth forget all about the user so we can try to authenticate again.
		
		// Display the recived error,
		// to know more please refer to Exceptions handling section on the userguide
		switch( $e->getCode() ){
		case 0 : $error = "Unspecified error."; break;
				case 1 : $error = "Hybriauth configuration error."; break;
				case 2 : $error = "Provider not properly configured."; break;
				case 3 : $error = "Unknown or disabled provider."; break;
				case 4 : $error = "Missing provider application credentials."; break;
				case 5 : $error = "Authentification failed. The user has canceled the authentication or the provider refused the connection."; break;
				case 6 : $error = "User profile request failed. Most likely the user is not connected to the provider and he should to authenticate again.";
				$adapter->logout();
		break;
		case 7 : $error = "User not connected to the provider.";
				$adapter->logout();
				break;
		}
			
			// well, basically your should not display this to the end user, just give him a hint and move on..
			$error .= "<br /><br /><b>Original error message:</b> " . $e->getMessage();
			$error .= "<hr /><pre>Trace:<br />" . $e->getTraceAsString() . "</pre>";
		}
		
		if( $error ){
			echo '<p><h3 style="color:red">Error!</h3>' . $error . '</p>';
			echo "<pre>Session:<br />" . print_r( $_SESSION, true ) . "</pre><hr />";
		}
		
		$contents = ob_get_contents();
		ob_flush();
		
		return $contents;
	}
	
	
}