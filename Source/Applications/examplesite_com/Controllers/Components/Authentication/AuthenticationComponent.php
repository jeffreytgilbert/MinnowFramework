<?php 

/*
 * @notes 
 * sign in is one thing, but registration is another. to capture social sign ins you need to also anticipate that the endpoint will
 * require you to use the same one over and over, so the end point logic needs to also account for new registrations. i'm now thinking 
 * about this from a registration point of view, not a signin point of view. 
 * 
 * when a social sign in happens, the page must determin the proper course of action:
 * First, check to see if the user is logged in.
 * If so, and the person has an account, add the sign in to the account login types if possible.
 * Next, check to see if the user is already attached to an account. If so, log them in.
 * Finally, if the user is not logged in and the account does not belong to a user in the db, register an account for the user, 
 * then prompt them for account information if needed afterwards. 
 * 
 */

class AuthenticationComponent extends Component{
	// this returns an object of type "user" or "UserAccount" and then the rest of the apps call it in the normal way. 
	// upon login, this thing has the data in it that will be cached in a system cache at the end of the page request,
	// and it just builds and builds until you logout or it expires. so now we can have caches of any sort stuck in this object
	// for anything related to the user and we dont have to update them until the end of the page request, but all that data is 
	// still available if the page needs it so there should be less query logic and page logic for this system cache idea i was going to do.
	// 
	// Also, because this is the login component here, it will have the functions / methods to set, unset, whatever cookies and sessions
	// and nothing will need to get cached immediately. This Authentication component should remain pretty easy to edit but
	// at the same time it should try to keep as much of the additional functionality it can outside itself

	private $ip					= '';
	private $proxy				= '';
	private $unvalidated_info	= array();
	private $location			= array();
	
	protected $_is_api_call		= false;
	private $_HybridAuthHelper, $_LocationHelper, $_SecureCookieHelper, $_SessionHelper, $_SecureHashHelper;
	
	public function getHybridAuth(){ return $this->_HybridAuthHelper; }
	public function getLocationHelper(){ return $this->_LocationHelper; }
	public function getSecureCookieHelper(){ return $this->_SecureCookieHelper; }
	public function getSecureHashHelper(){ return $this->_SecureHashHelper; }
	public function getSessionHelper(){ return $this->_SessionHelper; }
	
// 	const REQUEST_FULL_REGISTRATION = 'FullRegistration';
// 	const REQUEST_FINISH_REGISTRATION = 'FinishRegistration';
// 	const REQUEST_LOGIN = 'Login';
// 	const REQUEST_LOGOUT = 'Logout';
// 	const REQUEST_CONFIRM_CONTACT = 'ConfirmContact';
	
	// maybe these can get refactored to a component controller for login status, or maybe they stay here instead. 
// 	const _LOGGED_OUT_BY_REQUEST = 1;
// 	const _LOGGED_OUT_BAD_SESSION = 2;
// 	const _LOGGED_OUT_BY_REQUEST = 'logged out by request';
// 	const MSG_MANUAL_LOG_OUT = 0;
// 	const MSG_PASSWORD_CHANGED = 1;
// 	const MSG_SESSION_EXPIRED = 2;
// 	const MSG_INVALID_ACCOUNT = 3;
// 	const MSG_IP_MATCH_ERROR = 4;
// 	const MSG_INVALID_PASSWORD = 5;
// 	const MSG_TOKEN_MATCH_ERROR = 6;
// 	const MSG_IP_BAN = 7;
// 	const MSG_FORGOTTEN_PASSWORD_ERROR = 8;
// 	const MSG_PASSWORD_CHANGE_REQUEST = 9;
// 	const MSG_ACCOUNT_KILLED_BY_ADMIN = 10;
// 	const MSG_REOPENED_ACCOUNT = 11;
// 	const MSG_LOGIN_REQUIRED = 12;
// 	const MSG_KNOWN_ERROR = 13;
// 	const MSG_SYSTEM_ERROR = 14;
// 	const MSG_UNKNOWN_ERROR = 15;
	
	public static function cast(Component $AuthenticationComponent){ 
		if($AuthenticationComponent instanceof AuthenticationComponent) { return $AuthenticationComponent; }
	}
	
	public function __construct(Controller $Controller, Model $Settings){
		parent::__construct($Controller, $Settings);
		
		$this->_Controller->loadModels(array(
			'UserAccount',
			'UserLoginProvider',
			'UserLogin',
			'AccessRequest',
			'OnlineMember',
			'OnlineGuest',
			'UserSession'
		));
		
		Run::fromComponents('Authentication/AuthenticationException.php');
		Run::fromComponents('Authentication/Models/AuthenticationCookie.php');
		
		$this->_Controller->loadActions(array(
			'PhpSessionActions',// not sure i need this one
			'UserSessionActions',
			'UserLoginProviderActions',
			'UserAccountActions',
			'UserLoginActions',
			'UserSessionActions',
			'OnlineGuestActions',
			'OnlineMemberActions'
		));
		
		$this->_HybridAuthHelper = $this->_Controller->getHelpers()->HybridAuth();
		$this->_LocationHelper = $this->_Controller->getHelpers()->Location();
		$this->_SecureCookieHelper = $this->_Controller->getHelpers()->SecureCookie();
		$this->_SecureHashHelper = $this->_Controller->getHelpers()->SecureHash();
		$this->_SessionHelper = $this->_Controller->getHelpers()->Session();
	}
	
	public function __destruct(){}
	
	public function getInstance(){
		if($this->_instance instanceof AuthenticationComponent){ return $this->_instance; }
	}
	
	public function getProviderList(){
		return $this->_HybridAuthHelper->getAvailableProviders();
	}

	public function getConnectedProviderList(){
		return $this->_HybridAuthHelper->getConnectedProviders();
	}
	
	public function logout(){
		if(isset($_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID'])){
			$ID = unserialize($_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID']);
			
			if($ID instanceof AccessRequest && $ID->isOnline()){
				$ID = OnlineMember::cast($ID);
				// delete user session in the db
				UserSessionActions::deleteUserSessionByPhpSessionId(session_id());
			}
			
			// delete php session data
			unset($_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID']);
		}
		
		// delete social logins
		$this->getHybridAuth()->logoutAllProviders();
		
		// delete secure cookie
		$CookieHelper = RuntimeInfo::instance()->getHelpers()->SecureCookie();
		$CookieHelper->delete('MINNOW::COMPONENTS::AUTHENTICATION::ID');
	}
	
	private $_ID;
	public function identifyUser(){
		// cache
		if(isset($this->_ID)){ return $this->_ID; }
		
		// Check sessions
		if(isset($_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID'])){ 
			$ID = unserialize($_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID']);
			// make sure it's authentic.
			if($ID instanceof OnlineMember || $ID instanceof OnlineGuest){ return $ID; }
			// if it's not genuine, dump it and start over.
			else {
				unset($_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID']);
				return $ID = $this->authenticate();
			}
		} else {
			return $ID = $this->authenticate();
		}
		
	}
	
	const ADD_AUTHENTICATION_SUCCESS = 1;
	const ADD_AUTHENTICATION_ERROR_DUPLICATE_ENTRY = 2;
	const ADD_AUTHENTICATION_ERROR_NO_CONNECTED_PROVIDERS = 3;
	
	public function authenticateNewHybridAuthConnection(OnlineMember $ID){
		
//  		ob_end_flush();
//  		pr('frush');
		
		// Check social sign ins
		
		// collect a list of supported providers for validation of supported social sign in types
		$ValidUserLoginProviderCollection = UserLoginProviderActions::selectList();
		foreach($ValidUserLoginProviderCollection as $UserLoginProvider){
			$supported_providers[$UserLoginProvider->getString('user_login_provider_id')] = $UserLoginProvider->getString('provider_name');
		}
		
		// get a list of connected providers so the logins can be checked against the database for matches
		$connected_providers = $this->_HybridAuthHelper->getConnectedProviders();
// 		pr($connected_providers); // goes right to the source to see if there are any accounts logged in
		
		// if there are
		if(count($connected_providers) > 0){
			$HybridAuthApprovedUserLoginCollection = new UserLoginCollection();
			
			// check connected providers
			foreach($connected_providers as $connected_provider){
				$HybridAuthAdapter = $this->_HybridAuthHelper->getAdapter($connected_provider);
				if($HybridAuthAdapter->isUserConnected()){
					
					// Create the start of a user login in a collection for when the db is ready to insert to the db the new record
					$HybridAuthApprovedUserLoginCollection->addObject(new UserLogin(array(
// 						'user_id'=>$user_id,
						'unique_identifier'=>$HybridAuthAdapter->getUserProfile()->identifier,
						'user_login_provider_id'=>array_search($connected_provider, $supported_providers),
						'UserLoginProvider'=>new UserLoginProvider(array(
							'login_type'=>'HybridAuth',
							'provider_name'=>$connected_provider,
						)),
						'is_verified'=>true,
// 						'serialized_credentials'=>serialize($HybridAuthAdapter->getAccessToken())
 						'serialized_credentials'=>$this->_HybridAuthHelper->getSerializedCredentialsByProvider($connected_provider)
//						'serialized_credentials'=>$this->_HybridAuthHelper->getSerializedCredentials()
					)));
					
//					pr($this->_HybridAuthHelper->getSerializedCredentialsByProvider($connected_provider));
					
				}
			}
			
// 			pr($HybridAuthApprovedUserLoginCollection);
			
			// All the providers currently logged in
			$authenticated_identifiers = $HybridAuthApprovedUserLoginCollection->getUniqueArrayByField('unique_identifier');
			
// 			pr($authenticated_identifiers);
			
			// grab the ones in the collection that are connected to accounts
			$DatabaseUserLoginCollection = UserLoginActions::selectListByUniqueIdentifiers($authenticated_identifiers);
			
// 			pr($DatabaseUserLoginCollection);
			
			$authenticated_identifiers_in_db = $DatabaseUserLoginCollection->getUniqueArrayByField('unique_identifier');
			
// 			pr($authenticated_identifiers_in_db);
			
			$authenticated_identifiers_that_need_to_be_added = array();
			
			// compare with current identifiers
			foreach($authenticated_identifiers as $user_identifier){
// 				pr('authenticate_identifiers');
				if(!in($user_identifier, $authenticated_identifiers_in_db)){
// 					pr('save identifier');
					$authenticated_identifiers_that_need_to_be_added[] = $user_identifier;
				}
			}
			
// 			pr($authenticated_identifiers_that_need_to_be_added);
			
			if(count($authenticated_identifiers_that_need_to_be_added) > 0){
// 				pr('insert a new record at this point');
				foreach($authenticated_identifiers_that_need_to_be_added as $approved_identifier){
// 					pr('record: '.$approved_identifier);
					$UserLogin = $HybridAuthApprovedUserLoginCollection->getUserLoginByFieldValue('unique_identifier', $approved_identifier);
					$UserLogin->set('user_id',$ID->getInteger('user_id'));
					UserLoginActions::insertUserLogin($UserLogin);
				}
				// found and added a new connection to login list
				return self::ADD_AUTHENTICATION_SUCCESS;
			} else {
// 				pr('failed to get unique identifier. it belonged to someone else');
				
				$MyUserLoginCollection = UserLoginActions::selectListByUserId($ID->getInteger('user_id'));
				
				// unset all current social sign ons and reauth them all from the db.
				$this->getHybridAuth()->logoutAllProviders();
				
				$social_sign_ons = array();
				foreach($MyUserLoginCollection as $MyUserLogin){
					if(!in($MyUserLogin->getInteger('user_login_provider_id'),array(1,2))){ // @todo hard coded Minnow Auth provider ids. make dependent on hybrid auth types
						$temporary_array = unserialize($MyUserLogin->getString('serialized_credentials'));
						$social_sign_ons = array_merge($temporary_array,$social_sign_ons);
					}
				}
				$this->_HybridAuthHelper->setProviderCredentials($social_sign_ons);
				
				// no new entries. all were taken or already registered
				return self::ADD_AUTHENTICATION_ERROR_DUPLICATE_ENTRY;
			}
			
		} else {
// 			pr('Failed to get any connected providers');
			// no connected providers at all
			return self::ADD_AUTHENTICATION_ERROR_NO_CONNECTED_PROVIDERS;
		}
	}
	
	private function authenticate(){
		// We'll need this IP address for the object that's returned
		$users_ip = $this->_LocationHelper->guessIP();
		$users_proxy_ip = $this->_LocationHelper->guessProxyIP();
		
		$CookieHelper = RuntimeInfo::instance()->getHelpers()->SecureCookie();
		
		// See if we can get a location from this ip
		$LocationFromIp = $this->_LocationHelper->getLocationFromServices($users_ip);
		$NetworkAddress = new NetworkAddress(array(
			'ip'=>$users_ip,
			'proxy'=>$users_proxy_ip
		));
		
		// Otherwise check cookie
		if($CookieHelper->fetch('MINNOW::COMPONENTS::AUTHENTICATION::ID')) {
			$serialized_cookie_data = $CookieHelper->fetch('MINNOW::COMPONENTS::AUTHENTICATION::ID');
			$Cookie = unserialize($serialized_cookie_data);
			$Cookie = AuthenticationCookie::cast($Cookie);
			
			// see if the cookie has a user id in it
			if($Cookie->getInteger('user_id') > 0 && mb_strlen($Cookie->getInteger('access_token')) > 0){
				// if it does, check it against the settings registered to be checked in the configuration script
				if(
					($this->getConfig()->get('validate_cookie_against_ip') && $Cookie->get('ip') == $users_ip) ||
					($this->getConfig()->get('validate_cookie_against_user_agent') && $Cookie->get('user_agent') == $_SERVER['HTTP_USER_AGENT']) 
				){
					
					$ValidUserSession = UserSessionActions::selectByAccessToken($Cookie->get('access_token'),$Cookie->get('user_id'));
					
					if($this->getConfig()->get('validate_cookie_against_token') && $Cookie->get('access_token') && $ValidUserSession->get('access_token')){
						
						$user_id = $Cookie->get('user_id');
						
						// Grab the users account from the db
						$MyUserAccount = UserAccountActions::selectByUserAccountId($user_id);
						
						$ID = $this->createUserSession($MyUserAccount, $LocationFromIp, $NetworkAddress);
						
						return $ID;
					}
					
					// bad cookie. Delete it and check the rest of the auth steps.
					$CookieHelper->delete('MINNOW::COMPONENTS::AUTHENTICATION::ID');
				
				// Otherwise, if the cookie wasn't valid any longer, delete the cookie and any session data associated with it
				} else {
					$this->logout();
				}
			// Otherwise, the cookie was a guest cookie, so return this access request as a guest.
			} else {
				return $this->setUserAsGuest($CookieHelper, $LocationFromIp, $NetworkAddress, $users_ip, $users_proxy_ip);
			} // end guest vs member cookie check
			
		} // end cookie check
		
		// Otherwise check social sign ins
		
		// collect a list of supported providers for validation of supported social sign in types
		$ValidUserLoginProviderCollection = UserLoginProviderActions::selectList();
		foreach($ValidUserLoginProviderCollection as $UserLoginProvider){
			$supported_providers[$UserLoginProvider->getString('user_login_provider_id')] = $UserLoginProvider->getString('provider_name');
		}
		
		// get a list of connected providers so the logins can be checked against the database for matches
		$connected_providers = $this->_HybridAuthHelper->getConnectedProviders();
		//pr($connected_providers);
		if(count($connected_providers) > 0){
			$HybridAuthApprovedUserLoginCollection = new UserLoginCollection();
			
			// check providers
			foreach($connected_providers as $connected_provider){
				$HybridAuthAdapter = $this->_HybridAuthHelper->getAdapter($connected_provider);
				if($HybridAuthAdapter->isUserConnected()){
					
					// create a collection in preparation for if the users social account(s) 
					// need to be added to the users account because this linkup is new.
					$HybridAuthApprovedUserLoginCollection->addObject(new UserLogin(array(
// 						'user_id'=>$user_id,
						'unique_identifier'=>$HybridAuthAdapter->getUserProfile()->identifier,
						'user_login_provider_id'=>array_search($connected_provider, $supported_providers),
						'UserLoginProvider'=>new UserLoginProvider(array(
							'login_type'=>'HybridAuth',
							'provider_name'=>$connected_provider,
						)),
						'is_verified'=>true,
// 						'serialized_credentials'=>serialize($HybridAuthAdapter->getAccessToken())
 						'serialized_credentials'=>$this->_HybridAuthHelper->getSerializedCredentialsByProvider($connected_provider)
//						'serialized_credentials'=>$this->_HybridAuthHelper->getSerializedCredentials()
					)));
					
//					pr($this->_HybridAuthHelper->getSerializedCredentialsByProvider($connected_provider));
				}
			}
			
			// we want to go through each of these to see if they're not recorded yet.
			// if there are no user conflicts (having mulitple owners for the hybrid auth sessions logged in). 
			// TLDR unique identifiers for the sessions currently signed in for comparison with the ones recorded in the database.
			$authenticated_identifiers = $HybridAuthApprovedUserLoginCollection->getUniqueArrayByField('unique_identifier');
			
			// grab the ones in the collection that are connected to their account(s)
			$DatabaseUserLoginCollection = UserLoginActions::selectListByUniqueIdentifiers($authenticated_identifiers);
			// grab just the unique account ids 
			$user_ids = $DatabaseUserLoginCollection->getUniqueArrayByField('user_id');
			
			// make sure there is only 1 user these unique ids are attached to
			if(count($user_ids) == 1){ // there's one account that matches one or more of the hybrid auth providers that are linked to this session
				
				// if so, note the id
				$user_id = array_pop($user_ids);
				
			} else if (count($user_ids) > 1){ // multiple accounts on the site should be connected by social logins. there might need to be an account merge tool.
				
				// log all the hybrid auth sessions out if this happens. There is no account merge option at this time
				$this->_HybridAuthHelper->logoutAllProviders();
				return $this->setUserAsGuest($CookieHelper, $LocationFromIp, $NetworkAddress, $users_ip, $users_proxy_ip);
				
			} else { // there are no accounts associated with this/these logins, but there should be, so register one.
				
				if(!empty($HybridAuthAdapter->getUserProfile()->firstName) && !empty($HybridAuthAdapter->getUserProfile()->lastName)){
					$first_name = $HybridAuthAdapter->getUserProfile()->firstName;
					$last_name = $HybridAuthAdapter->getUserProfile()->lastName;
				} else if(!empty($HybridAuthAdapter->getUserProfile()->displayName)){
					$display_name = $HybridAuthAdapter->getUserProfile()->displayName;
					$name_pieces = explode(' ', $display_name, 2);
					if(count($name_pieces) > 1){
						$first_name = $name_pieces[0];
						$last_name = $name_pieces[1];
					} else {
						$first_name = $display_name;
						$last_name = '';
					}
				}
				
				$user_id = UserAccountActions::insertUserAccountFromHybridAuthRegistration(new UserAccount(array(
					'first_name'=>$first_name,
					'last_name'=>$last_name,
					'latitude'=>$LocationFromIp->get('latitude'),
					'longitude'=>$LocationFromIp->get('longitude'),
					'gmt_offset'=>$LocationFromIp->get('gmt_offset')
				)));
				
			}
			
			// get a collection of user logins matching this user id.
			// this is to check to see if all sessions exist on account already, so if any are missing, we can add them.
			$MyUserLoginCollection = UserLoginActions::selectListByUserId($user_id);
			
			// pull out just the unique identifiers that belong to this user and toss them in an array.
			$this_users_identifier_ids = $MyUserLoginCollection->getUniqueArrayByField('unique_identifier');
			
			// loop through sessions collection prepared earlier for entry into the db if needed
			foreach($HybridAuthApprovedUserLoginCollection as $UserLogin){
				
				// Check this unique identifier from the session against the registered unique identifiers for this account. 
				if(in($UserLogin->getString('unique_identifier'), $this_users_identifier_ids)){
					
					// If it exists in the db, may need to continue to the next check, but need to verify the providers match if so
					$matching_objects = $MyUserLoginCollection->getObjectArrayByFieldValue('unique_identifier', $UserLogin->getString('unique_identifier'));
					
					foreach($matching_objects as $Object){
						
						// if the providers match too, continue
						if($Object->getString('user_login_provider_id') == $UserLogin->getString('user_login_provider_id')){
							continue;
						// otherwise if there is no matching provider, enter it as new.
						} else {
							
// 							pr('Add this login to the account because none matched exactly in the logins table');
							
							// Set the user id associated with this identifier
							$UserLogin->set('user_id',$user_id);
							// Add it to the user logins table
							UserLoginActions::insertUserLogin($UserLogin);
							// Add the object to the collection of logins
							$MyUserLoginCollection->addObject($UserLogin);
						}
					}
				// If it doesnt exist at all, add login to users account
				} else {
					
// 					pr('Add this login to the account because one wasnt found that matched at all in the login table');
					
					// Set the user id associated with this identifier
					$UserLogin->set('user_id',$user_id);
					// Add it to the user logins table
					UserLoginActions::insertUserLogin($UserLogin);
					// Add the object to the collection of logins
					$MyUserLoginCollection->addObject($UserLogin);
				}
			}
			
			// Note: $MyUserLoginCollection should now be a complete collection of current logins from the db and the new sessions
			
			// Grab the users account from the db
			$MyUserAccount = UserAccountActions::selectByUserAccountId($user_id);
			
			// Create the users session and give back an ID that can be returned 
			$ID = $this->createUserSession($MyUserAccount, $LocationFromIp, $NetworkAddress);
			
			// Now, whichever social sign ins are not currently logged in, log them in.
			
			// get all the unique ids that are signed in currently
			$unique_identifiers_from_session = $HybridAuthApprovedUserLoginCollection->getUniqueArrayByField('unique_identifier');
			
			// Make a collection of UserLogin's that are specific to HybridAuth
			$MyHybridAuthUserLoginCollection = new UserLoginCollection($MyUserLoginCollection->getObjectArrayByFieldValue('login_type', 'HybridAuth'));
			
			$social_sign_ons = array();
			foreach($MyUserLoginCollection as $MyUserLogin){
				if(!in($MyUserLogin->getInteger('user_login_provider_id'),array(1,2))){ // @todo hard coded Minnow Auth provider ids. make dependent on hybrid auth types
					$temporary_array = unserialize($MyUserLogin->getString('serialized_credentials'));
					$social_sign_ons = array_merge($temporary_array,$social_sign_ons);
				}
			}
			$this->_HybridAuthHelper->setProviderCredentials($social_sign_ons);			
			
// 			// First, compare current logins to existing logins, and if any are not logged in yet, log them in
// 			foreach($MyHybridAuthUserLoginCollection as $UserLogin){
				
// 				// is the key from the db not logged in?
// 				if( !in($UserLogin->getString('unique_identifier'), $unique_identifiers_from_session) ){
					
// // 					// log it in
// // 					if(!in($UserLogin->getInteger('user_login_provider_id'),array(1,2))){ // @todo hard coded Minnow Auth provider ids. make dependent on hybrid auth types
// // 						$this->_HybridAuthHelper->setProviderCredentials(unserialize($UserLogin->getString('serialized_credentials')));
// // 					}
					
// 					$this->_HybridAuthHelper->setProviderCredentials(unserialize($UserLogin->getString('serialized_credentials')));
					
// 				}
// 			}
			
			// Set a cookie so this person can log back in later when their session expires. Always do this when logging in from single sign on. ( @todo or do we? Test this )
			$this->setRememberMeCookie($ID);
			
			return $ID;
			
		} else {
			return $this->setUserAsGuest($CookieHelper, $LocationFromIp, $NetworkAddress, $users_ip, $users_proxy_ip);
		}
	}
	
	public function authenticateForm(DataObject $Form){
		
		// If login request is legit, log out anyone currently logged in to prevent issues with logins
		$this->logout();
		
		// Query the db for a login
		$UserLogin = UserLoginActions::selectByUniqueIdentifierAndProviderTypeId(
			$Form->getString('unique_identifier'),
			1 // 1 is the id for Email auth through minnow auth component
		);
		
		// If there was a match in the db, check it against the account holders password
		if($UserLogin->getInteger('user_login_id') > 0){
			
			// Check last login against login attempts and if the account is locked, skip the rest of the login check

			// First, get the last login time
			$LastLoginDateTimeObject = clone $UserLogin->getDateTimeObject('last_failed_attempt');
			
			// Then you want to get what time it is now
			$Now = clone RuntimeInfo::instance()->now();
			
			// Add 15 minutes to the last login time (or a time from the components settings for brute force timeout)
			$LastLoginDateTimeObject->add(DateInterval::createFromDateString('+15 minutes'));
			
			// Get the number of seconds it would have been 
			$difference = $Now->getTimestamp() - $LastLoginDateTimeObject->getTimestamp();
			
			if(
				$difference > 0 || // if the time limit has expired since the last attempt
				$UserLogin->getInteger('current_failed_attempts') < 15 // or if the current failed attempts are very low
			){
				UserLoginActions::resetFailedAttemptCounter($UserLogin->getInteger('user_login_id'));
				
				$UserAccount = UserAccountActions::selectByUserAccountId($UserLogin->getInteger('user_id'));
				
				// If the password exists and is valid, log the user in
				if(
					$UserAccount->getString('password_hash') != '' &&
					$this->getSecureHashHelper()->validatePassword($Form->getString('password'),$UserAccount->getString('password_hash'))
				){
					// @todo check to see if account is closed, and if it is, reopen it before logging in
					
					$users_ip = $this->_LocationHelper->guessIP();
					$users_proxy_ip = $this->_LocationHelper->guessProxyIP();
					
					// See if we can get a location from this ip
					$LocationFromIp = $this->_LocationHelper->getLocationFromServices($users_ip);
					$NetworkAddress = new NetworkAddress(array(
						'ip'=>$users_ip,
						'proxy'=>$users_proxy_ip
					));
					
					// create the users session
					$ID = $this->createUserSession($UserAccount, $LocationFromIp, $NetworkAddress);
					
					// Log in all the social sign ons
					$MyUserLoginCollection = UserLoginActions::selectListByUserId($ID->getInteger('user_id'));
					
					// unset all current social sign ons and reauth them all from the db.
					$this->getHybridAuth()->logoutAllProviders();
					
					$social_sign_ons = array();
					foreach($MyUserLoginCollection as $MyUserLogin){
						if(!in($MyUserLogin->getInteger('user_login_provider_id'),array(1,2))){ // @todo hard coded Minnow Auth provider ids. make dependent on hybrid auth types
							$temporary_array = unserialize($MyUserLogin->getString('serialized_credentials'));
							$social_sign_ons = array_merge($temporary_array,$social_sign_ons);
						}
					}
					$this->_HybridAuthHelper->setProviderCredentials($social_sign_ons);
					
					// if requested, set a login cookie using this data
					if(1){ // @todo remember me cookie
						$this->setRememberMeCookie($ID);
					}
					
// 					pr($_SESSION);
					
					return $ID;
					
				} else {
					UserLoginActions::iterateAttemptCount($UserLogin->getInteger('user_login_id'));
					throw new AuthenticationException(
						'Sorry, we could not log you in with those credentials.', 
						AuthenticationException::BAD_CREDENTIALS
					);
				}
				
			} else {
				UserLoginActions::iterateAttemptCount($UserLogin->getInteger('user_login_id'));
				throw new AuthenticationException(
					'Account temporarily locked due to an excess of bad attempts. Account will be reactivated in 15 minutes.', 
					AuthenticationException::TOO_MANY_BAD_REQUESTS
				);
			}
			
		} else {
			throw new AuthenticationException(
				'Sorry, no user could be found with those credentials.', 
				AuthenticationException::USER_ACCOUNT_NOT_REGISTERED
			);
		}		
		
	}
	
	private function createUserSession(UserAccount $UserAccount, LocationFromIp $LocationFromIp, NetworkAddress $NetworkAddress){
		
		// The user needs to be marked as online in the database.
		UserAccountActions::setUserAsOnline($UserAccount->getInteger('user_id'));
		
		// The account info should be stored in a member object. 
		// The member object should inherit from the same parent class as other ID types, ex: Guest, Member, Application, etc. 
		// Not every auth request contains user info, and in fact none of them contain the same data, but all would have similar 
		// methods like isOnline(), isApiRequest(), and other such identifiers so common checks can be performed quickly.
		
		$ID = new OnlineMember(array(
			'user_id'=>$UserAccount->getInteger('user_id'),
			'UserAccount'=>$UserAccount,
			'last_active'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
			'LocationFromIp'=>$LocationFromIp,
			'NetworkAddress'=>$NetworkAddress
		));
		
		// Set this requesters session data so subsequent page loads dont try to log him in
		$_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID'] = serialize($ID);
		
		// just need to generate a unique code. dont need to decrypt it at any time. 
		$UserSession = new UserSession(array(
			'user_id'=>$UserAccount->getInteger('user_id'),
			'UserAccount'=>$ID->getUserAccount(),
			'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
			'last_access'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
			'ip'=>$NetworkAddress->getString('ip'),
			'proxy'=>$NetworkAddress->getString('proxy'),
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'access_token'=>$this->getParentController()->getHelpers()->SecureHash()->generateSecureHash(UserSessionActions::createRandomCode()),
			'php_session_id'=>session_id()
		));
		
		$ID->set('UserSession',$UserSession);
		
		UserSessionActions::insertUserSession($UserSession); // insert a user session to track this login and to secure cookies against
		
		OnlineMemberActions::insertOnlineMember($ID); // create a unique counter impression for members stats
		
		return $ID;
	}
	
	public function setRememberMeCookie(OnlineMember $ID){
		
		// Member cookie
		$AuthenticationCookie = new AuthenticationCookie(array(
			'user_id'=>$ID->get('user_id'),
			'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
			'ip'=>$ID->getNetworkAddress()->get('ip'),
			'proxy'=>$ID->getNetworkAddress()->get('proxy'),
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'access_token'=>$ID->getUserSession()->get('access_token')
		));
		
		// Cookies link back to the user sessions table to validate against the token in the db
		$CookieHelper = RuntimeInfo::instance()->getHelpers()->SecureCookie();
		$CookieHelper->store('MINNOW::COMPONENTS::AUTHENTICATION::ID', serialize($AuthenticationCookie));
	}
	
	private function setUserAsGuest($CookieHelper, $LocationFromIp, $NetworkAddress, $users_ip, $users_proxy_ip){
			
		$ID = new OnlineGuest(array(
			'php_session_id'=>session_id(),
			'last_active'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
			'LocationFromIp'=>$LocationFromIp,
			'NetworkAddress'=>$NetworkAddress,
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat()
		));
		
		// Set this requesters session data so subsequent page loads dont try to log him in
		$_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID'] = serialize($ID);

		OnlineGuestActions::insertOnlineGuest($ID);
		
		// don't save a cookie because guests should be session based only
		
		return $ID;
	}
	
}