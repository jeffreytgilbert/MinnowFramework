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
 * Another thought during this process. What if the developer gets annoyed that the sessions are not encapsulated in their own namespace like HA::STORE is. 
 * Should probably fix the sessions so they are in their own space before declaring this a 1.0
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
	public function getSessionHelper(){ return $this->_SessionHelper; }
	
// 	const REQUEST_FULL_REGISTRATION = 'FullRegistration';
// 	const REQUEST_FINISH_REGISTRATION = 'FinishRegistration';
// 	const REQUEST_LOGIN = 'Login';
// 	const REQUEST_LOGOUT = 'Logout';
// 	const REQUEST_CONFIRM_CONTACT = 'ConfirmContact';
	
	// maybe these can get refactored to a component controller for login status, or maybe they stay here instead. 
	const _LOGGED_OUT_BY_REQUEST = 1;
	const _LOGGED_OUT_BAD_SESSION = 2;
// 	const _LOGGED_OUT_BY_REQUEST = 'logged out by request';
	const MSG_MANUAL_LOG_OUT = 0;
	const MSG_PASSWORD_CHANGED = 1;
	const MSG_SESSION_EXPIRED = 2;
	const MSG_INVALID_ACCOUNT = 3;
	const MSG_IP_MATCH_ERROR = 4;
	const MSG_INVALID_PASSWORD = 5;
	const MSG_TOKEN_MATCH_ERROR = 6;
	const MSG_IP_BAN = 7;
	const MSG_FORGOTTEN_PASSWORD_ERROR = 8;
	const MSG_PASSWORD_CHANGE_REQUEST = 9;
	const MSG_ACCOUNT_KILLED_BY_ADMIN = 10;
	const MSG_REOPENED_ACCOUNT = 11;
	const MSG_LOGIN_REQUIRED = 12;
	const MSG_KNOWN_ERROR = 13;
	const MSG_SYSTEM_ERROR = 14;
	const MSG_UNKNOWN_ERROR = 15;
	
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
		$this->_SecureHash = $this->_Controller->getHelpers()->SecureHash();
		$this->_SessionHelper = $this->_Controller->getHelpers()->Session();
	}
	
	public function __destruct(){}
	
	public function getInstance(){
		if($this->_instance instanceof AuthenticationComponent){ return $this->_instance; }
	}
	
	public function getProviderList(){
		return $this->_HybridAuthHelper->getAvailableProviders();
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
	
	private function authenticate(){
		// We'll need this IP address for the object that's returned
		$users_ip = $this->_LocationHelper->guessIP();
		$users_proxy_ip = $this->_LocationHelper->guessProxyIP();
		
		$CookieHelper = RuntimeInfo::instance()->getHelpers()->SecureCookie();
		
		// See if we can get a location from this ip
		$LocationFromIp = $this->_LocationHelper->getLocationFromServices($users_ip);
		$NetworkAddress = new NetworkAddress(array(
			'ip'=>$LocationFromIp->get('ip'),
		));
		
		// Otherwise check cookie
		if($CookieHelper->fetch('MINNOW::COMPONENTS::AUTHENTICATION::ID')) {
			$serialized_cookie_data = $CookieHelper->fetch('MINNOW::COMPONENTS::AUTHENTICATION::ID');
			$Cookie = unserialize($serialized_cookie_data);
			$Cookie = AuthenticationCookie::cast($Cookie);
			
			// see if the cookie has a user id in it
			if($Cookie->getInteger('user_id') > 0 && strlen($Cookie->getInteger('access_token')) > 0){
				// if it does, check it against the settings registered to be checked in the configuration script
				if(
					($this->getConfig()->get('validate_cookie_against_ip') && $Cookie->get('ip') == $users_ip) ||
					($this->getConfig()->get('validate_cookie_against_user_agent') && $Cookie->get('user_agent') == $_SERVER['HTTP_USER_AGENT']) 
				){
					
					$ValidUserSession = UserSessionActions::selectByAccessToken($Cookie->get('access_token'),$Cookie->get('user_id'));
					
					if($this->getConfig()->get('validate_cookie_against_token') && $Cookie->get('access_token') && $ValidUserSession->get('access_token')){
						
						$user_id = $Cookie->get('user_id');
						
						//------------------------------------------------------
						//------------------------------------------------------
						
						// The user needs to be marked as online in the database.
						UserAccountActions::setUserAsOnline($user_id);
						
						// Grab the users account from the db
						$MyUserAccount = UserAccountActions::selectByUserAccountId($user_id);
						
						// The account info should be stored in a member object. 
						// The member object should inherit from the same parent class as other ID types, ex: Guest, Member, Application, etc. 
						// Not every auth request contains user info, and in fact none of them contain the same data, but all would have similar 
						// methods like isOnline(), isApiRequest(), and other such identifiers so common checks can be performed quickly.
						
						$ID = new OnlineMember(array(
							'user_id'=>$user_id,
							'UserAccount'=>$MyUserAccount,
							'last_active'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
							'LocationFromIp'=>$LocationFromIp,
							'NetworkAddress'=>$NetworkAddress
						));
						
						// Set this requesters session data so subsequent page loads dont try to log him in
						$_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID'] = serialize($ID);
						
						// just need to generate a unique code. dont need to decrypt it at any time. 
						$UserSession = new UserSession(array(
							'user_id'=>$user_id,
							'UserAccount'=>$ID->getUserAccount(),
							'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
							'last_access'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
							'ip'=>$users_ip,
							'proxy'=>$users_proxy_ip,
							'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
							'access_token'=>$this->getParentController()->getHelpers()->SecureHash()->generateSecureHash(UserSessionActions::createRandomCode()),
							'php_session_id'=>session_id()
						));
						
						UserSessionActions::insertUserSession($UserSession); // insert a user session to track this login and to secure cookies against
						
						OnlineMemberActions::insertOnlineMember($ID); // create a unique counter impression for members stats
						
						// Cookies should eventually expire. 
						
						// Member cookie
// 						$AuthenticationCookie = new AuthenticationCookie(array(
// 							'user_id'=>$user_id,
// 							'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
// 							'ip'=>$users_ip,
// 							'proxy'=>$users_proxy_ip,
// 							'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
// 							'access_token'=>$UserSession->getString('access_token')
// 						));
						
// 						$CookieHelper->store('MINNOW::COMPONENTS::AUTHENTICATION::ID', serialize($AuthenticationCookie));
						
						mail('jeffreytgilbert@gmail.com','Cookie was good','The cookie you saved was successfully used to reauthenticate the user after the session had expired.');
						
						return $ID;
						
						//------------------------------------------------------
						//------------------------------------------------------						
						
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
						'serialized_credentials'=>serialize($HybridAuthAdapter->getAccessToken())
					)));

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
				
				$user_id = UserAccountActions::insertUserAccountFromHybridAuthRegistration(new UserAccount(array(
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
			
			// The user needs to be marked as online in the database.
			UserAccountActions::setUserAsOnline($user_id);
			
			// Grab the users account from the db
			$MyUserAccount = UserAccountActions::selectByUserAccountId($user_id);
			
			// The account info should be stored in a member object. 
			// The member object should inherit from the same parent class as other ID types, ex: Guest, Member, Application, etc. 
			// Not every auth request contains user info, and in fact none of them contain the same data, but all would have similar 
			// methods like isOnline(), isApiRequest(), and other such identifiers so common checks can be performed quickly.
			
			$ID = new OnlineMember(array(
				'user_id'=>$user_id,
				'UserAccount'=>$MyUserAccount,
				'last_active'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
				'LocationFromIp'=>$LocationFromIp,
				'NetworkAddress'=>$NetworkAddress
			));
			
			// Now, whichever social sign ins are not currently logged in, log them in.
			
			// get all the unique ids that are signed in currently
			$unique_identifiers_from_session = $HybridAuthApprovedUserLoginCollection->getUniqueArrayByField('unique_identifier');
			
			// Make a collection of UserLogin's that are specific to HybridAuth
			$MyHybridAuthUserLoginCollection = new UserLoginCollection($MyUserLoginCollection->getObjectArrayByFieldValue('login_type', 'HybridAuth'));
			
			// First, compare current logins to existing logins, and if any are not logged in yet, log them in
			foreach($MyHybridAuthUserLoginCollection as $UserLogin){
				
				// is the key from the db not logged in?
				if( !in($UserLogin->getString('unique_identifier'), $unique_identifiers_from_session) ){
					
					// log it in
					$this->_HybridAuthHelper->setProviderCredentials($UserLogin->getString('serialized_credentials'));
					
				}
			}
			
			//------------------------------------------------------
			//------------------------------------------------------
			
			// Set this requesters session data so subsequent page loads dont try to log him in
			$_SESSION['MINNOW::COMPONENTS::AUTHENTICATION::ID'] = serialize($ID);
			
			// just need to generate a unique code. dont need to decrypt it at any time. 
			$UserSession = new UserSession(array(
				'user_id'=>$user_id,
				'UserAccount'=>$ID->getUserAccount(),
				'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
				'last_access'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
				'ip'=>$users_ip,
				'proxy'=>$users_proxy_ip,
				'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
				'access_token'=>$this->getParentController()->getHelpers()->SecureHash()->generateSecureHash(UserSessionActions::createRandomCode()),
				'php_session_id'=>session_id()
			));
			
			UserSessionActions::insertUserSession($UserSession); // insert a user session to track this login and to secure cookies against
			
			OnlineMemberActions::insertOnlineMember($ID); // create a unique counter impression for members stats
			
			// Member cookie
			$AuthenticationCookie = new AuthenticationCookie(array(
				'user_id'=>$user_id,
				'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
				'ip'=>$users_ip,
				'proxy'=>$users_proxy_ip,
				'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
				'access_token'=>$UserSession->getString('access_token')
			));
			
			// Cookies link back to the user sessions table to validate against the token in the db
			$CookieHelper->store('MINNOW::COMPONENTS::AUTHENTICATION::ID', serialize($AuthenticationCookie));
			
			return $ID;
			
			//------------------------------------------------------
			//------------------------------------------------------
			
		} else {
			return $this->setUserAsGuest($CookieHelper, $LocationFromIp, $NetworkAddress, $users_ip, $users_proxy_ip);
		}
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
		
		// Guest cookie
		$AuthenticationCookie = new AuthenticationCookie(array(
			'user_id'=>0,
			'created_datetime'=>RuntimeInfo::instance()->now()->getMySQLFormat(),
			'ip'=>$users_ip,
			'proxy'=>$users_proxy_ip,
			'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
			'access_token'=>''
		));
		
		$CookieHelper->store('MINNOW::COMPONENTS::AUTHENTICATION::ID', serialize($AuthenticationCookie));
		
		return $ID;
	}
	
	/*
	
	public function authenticateFromForm($login, $password){
		// check form
			$login=$_POST['login']['l'];
		$hash_password=e($_POST['login']['p']);
		// echo $hash_password;
		if(isset($_POST['login']['s']) && $_POST['login']['s'] == 'true') { $secure=true; }
		else { $secure=false; }
		
		//clear old failed attempts if last login attempt was longer than 15 minutes ago
		parent::MySQLUpdateAction('
			UPDATE user_account SET current_failed_attempts = 0 
			WHERE login_name = :login_name AND 
				last_failed_attempt BETWEEN (NOW() - INTERVAL 10 YEAR) AND 
				(NOW() - INTERVAL 15 MINUTE)',
			array(':login_name'=>$login)
		);
		
		$Result = parent::MySQLReadAction('
			SELECT user_id, `password`, is_killed, is_suicide, current_failed_attempts, last_failed_attempt 
			FROM user_account WHERE login_name = :login_name',
			array(':login_name'=>$login)
		)->getItemAt(0);
		
		if(isset($Result) && $Result->get('user_id','Is::set') && ($Result->get('current_failed_attempts') > 15 
		&& CalculateDate::difference($Result->get('last_failed_attempt')) < 3600)) // 15 attempts per hour
		{
			$this->recordLoginHistory('Excessive login attempts', $Result->get('user_id'), 0);
			$this->logout();
			prompt_login(8); // Your account has been disabled temporarily due to excessive login attempts. Please try again later, and if you\'re having trouble logging in check out "FAQ" and "Contact Us" Pages.
			return false;
		}
		// If the account isnt being hacked, check to see if its valid.
		else if(isset($Result) && $Result->get('user_id','Is::set'))
		{
			$user_id=$Result->get('user_id');
			
			if($Result->getData('is_killed','Is::set') && !$Result->getData('is_suicide','Is::set'))
			{
				$this->recordLoginHistory('Killed account', $user_id, 0);
				$this->logout();
				prompt_login(10); // This account has been closed for abusive behavior.
				return false;
			}
			else 
			{
				// Does the account login exist, and if so do the passwords match?
				
				if($Result->getData('is_suicide','Is::set')){ 
//					echo 'I think this is a suicide.';
					$this->redirect('/Account/Reopen/'); 
				}
				
				if($hash_password === $Result->get('password')){
					// Success! Create a session and a cookie
					$timeout=$this->createSecureCookie($user_id,$hash_password,$secure);			
					$this->createSessionReference($user_id,$timeout);
					// Reset the bad login counter //last_failed_attempt="0000-00-00 00:00:00" '
					parent::MySQLUpdateAction('
						UPDATE user_account 
						SET current_failed_attempts=0 
						WHERE user_id=:user_id',
						array(':user_id'=>(int)$user_id),
						array(':user_id')
					);
					
					// Record this event to login history
					$this->recordLoginHistory('Successful Login', $user_id, 1);
					$ID = new Member();
					$ID->loginAsUserId($user_id);
					$ID->setLocation($this->location);
					$_SESSION['ID']=serialize($ID);
					$this->updateMembersOnline($user_id);
					return $ID;
				}
				// If the account exists but the password doesnt match, record the bad attempt
				else
				{
					parent::MySQLUpdateAction('
						UPDATE user_account 
						SET current_failed_attempts=current_failed_attempts+1, 
							total_failed_attempts=total_failed_attempts+1, 
							last_failed_attempt=:last_failed_attempt 
						WHERE user_id=:user_id',
						array(
							':user_id'=>(int)$user_id, 
							':last_failed_attempt'=>RIGHT_NOW_GMT
						),
						array(':user_id')
					);
					$this->recordLoginHistory('Bad password', $user_id, 0);
					$this->logout();
					prompt_login(5); // Your login attempt failed because the information you supplied was invalid.
					return false;
				}
			}
		}
		// The account is dead or the login information is gone. Report this to the user.
		else
		{
//			echo __LINE__.'<br>';
			$this->recordLoginHistory('Dead account', 0 , 0);
			$this->logout();
			prompt_login(3); // The login you were attempting to use does not exist. Please make sure you are typing an Email address for your user name, not your screen name.
			return false;
		}
		
	}
	
	*/
}