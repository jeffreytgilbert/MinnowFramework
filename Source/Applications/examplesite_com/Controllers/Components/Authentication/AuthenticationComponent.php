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
	private $_HybridAuthHelper, $_LocationHelper;
	
	const REQUEST_FULL_REGISTRATION = 'FullRegistration';
	const REQUEST_FINISH_REGISTRATION = 'FinishRegistration';
	const REQUEST_LOGIN = 'Login';
	const REQUEST_LOGOUT = 'Logout';
	const REQUEST_CONFIRM_CONTACT = 'ConfirmContact';
	
	// maybe these can get refactored to a component controller for login status, or maybe they stay here instead. 
	const _LOGGED_OUT_BY_REQUEST = 1;
	const _LOGGED_OUT_BAD_SESSION = 2;
// 	const _LOGGED_OUT_BY_REQUEST = 'logged out by request';
// 	const _LOGGED_OUT_BY_REQUEST = 'logged out by request';
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
		if($AuthenticationComponent instanceof AuthenticationComponent) { return $AuthenticationComponent;}
	}
	
	public function __construct(Controller $Controller, Model $Settings){
		parent::__construct($Controller, $Settings);
		
		$this->_Controller->loadModels(array(
			'UserAccount',
			'UserLoginProvider',
			'UserLogin',
			'AccessRequest',
			'OnlineMember',
			'OnlineGuest'
		));
		
		$this->_Controller->loadActions(array(
			'PhpSessionActions',// not sure i need this one
			'UserSessionActions',
			'UserLoginProviderActions',
			'UserAccountActions',
			'UserLoginActions'
		));
		
		$this->_HybridAuthHelper = $this->_Controller->getHelpers()->HybridAuth();
		$this->_LocationHelper = $this->_Controller->getHelpers()->Location();
	}
	
	public function __destruct(){}
	
	public function getInstance(){
		if($this->_instance instanceof AuthenticationComponent){ return $this->_instance; }
	}
	
	
	public function authenticateFromHybridAuth(){
		
		if(isset($_SESSION['MINNOW::ID'])){ 
			$ID = unserialize($_SESSION['MINNOW::ID']);
			// make sure it's authentic.
			if($ID instanceof AccessRequest){ return $ID; }
			// if it's not genuine, dump it and start over.
			else { unset($_SESSION['MINNOW::ID']); }
		}
		
		// We'll need this IP address for the object that's returned
		$LocationFromIp = $this->_LocationHelper->getLocationFromServices($this->_LocationHelper->guessIP());
		$NetworkAddress = new NetworkAddress(array(
			'ip'=>$LocationFromIp->get('ip'),
		));
		
		// collect a list of supported providers for validation of supported social sign in types
		$ValidUserLoginProviderCollection = UserLoginProviderActions::selectList();
		foreach($ValidUserLoginProviderCollection as $UserLoginProvider){
			$supported_providers[$UserLoginProvider->getString('user_login_provider_id')] = $UserLoginProvider->getString('provider_name');
		}
		
// 		pr($ValidUserLoginProviderCollection);
// 		pr($supported_providers);
		
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
			
			// this is to check to see whats actually in the access token
// 			pr('$authenticated_identifiers');
// 			pr($authenticated_identifiers);
// 			pr('$HybridAuthApprovedUserLoginCollection');
// 			pr($HybridAuthApprovedUserLoginCollection);
// 			pr('$DatabaseUserLoginCollection');
// 			pr($DatabaseUserLoginCollection);
// 			pr('$user_ids');
// 			pr($user_ids);
			
			// make sure there is only 1 user these unique ids are attached to
			if(count($user_ids) == 1){ // there's one account that matches one or more of the hybrid auth providers that are linked to this session
				
				// if so, note the id
				$user_id = array_pop($user_ids);
				
			} else if (count($user_ids) > 1){ // multiple accounts on the site should be connected by social logins. there might need to be an account merge tool.
				// log all the hybrid auth sessions out if this happens. There is no account merge option at this time
				$this->_HybridAuthHelper->logoutAllProviders();
				
				$ID = new OnlineGuest(array(
					'php_session_id'=>session_id(),
					'last_active'=>'now',
					'LocationFromIp'=>$LocationFromIp,
					'NetworkAddress'=>$NetworkAddress
				));
				$_SESSION['MINNOW::ID'] = serialize($ID);
				return $ID;
				
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
			
//  			pr('$MyUserLoginCollection');
//  			pr($MyUserLoginCollection);
//  			pr('$this_users_identifier_ids');
//  			pr($this_users_identifier_ids);
			
			// loop through sessions collection prepared earlier for entry into the db if needed
			foreach($HybridAuthApprovedUserLoginCollection as $UserLogin){
				
// 				pr($this_users_identifier_ids);
// 				pr($UserLogin->getString('unique_identifier'));
				
				// Check this unique identifier from the session against the registered unique identifiers for this account. 
				if(in($UserLogin->getString('unique_identifier'), $this_users_identifier_ids)){
					
					// If it exists in the db, may need to continue to the next check, but need to verify the providers match if so
					$matching_objects = $MyUserLoginCollection->getObjectArrayByFieldValue('unique_identifier', $UserLogin->getString('unique_identifier'));
					
// 					pr('$matching_objects');
// 					pr($matching_objects);
					
					foreach($matching_objects as $Object){
						
						// if the providers match too, continue
						if($Object->getString('user_login_provider_id') == $UserLogin->getString('user_login_provider_id')){
							continue;
						// otherwise if there is no matching provider, enter it as new.
						} else {
							
// 							pr('Add this login to the account because none matched exactly in the logins table');
// 							pr('$UserLogin');
// 							pr($UserLogin);
							
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
// 					pr('$UserLogin');
// 					pr($UserLogin);
					
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
			
			// The login info should be stored in a session
			// $_SESSION['ID'] = '';
			
			$ID = new OnlineMember(array(
				'user_id'=>$user_id,
				'UserAccount'=>$MyUserAccount,
				'last_active'=>'now',
				'LocationFromIp'=>$LocationFromIp,
				'NetworkAddress'=>$NetworkAddress
			));
			
			// Cookies should be made to link back to the sessions
			//
			// so this is where a secure cookie needs to be created. ideas?
			// needs to be decryptable, so it can be secure from cookie forgers through:
			// encryption, containing an expiration date, validation against an authorization token stored in cache
			// if the token cant be found in the cache, then delete the cookie and log the user out. consider them expired or retired
			
			
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
			
			$_SESSION['MINNOW::ID'] = serialize($ID);
			return $ID;
			
		} else {
			$ID = new OnlineGuest(array(
				'php_session_id'=>session_id(),
				'last_active'=>'now',
				'LocationFromIp'=>$LocationFromIp,
				'NetworkAddress'=>$NetworkAddress
			));
			$_SESSION['MINNOW::ID'] = serialize($ID);
			// no providers connected, so couldn't log in with hybrid auth
//			return new Guest();
		}
	}
	
	/*
	
	// 		$this->_HybridAuth->authenticate($provider);
	
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
	
	// meant to be a shortcut function, but may never be used as originally intended.
	public function reauthenticate(){
		// check session
//		$this->_HybridAuthHelper->getHybridAuthInstance()->restoreSessionData($session_data); // session data is the tokenized/serialized string you get from getSessionData()
		
		// if no session exists, check cookie
		
		// if no cookie exists, dont login
	}
	
	public function checkLogin(){
		
//		$location = $this->_LocationHelper->getLocationFromServices($this->_Location->guessIP());
		
		// this should be desired functionality. If you api login request fails, you should just receive no data, not your data.
		
		// If the user is logging in we should destroy any existing session or cookie data 
		// and authenticate them via post with database data.
		if(isset($_SESSION['ID']))
		{
//			pr('session');
//			pr($_SESSION['ID']);
			$ID=$this->checkSessionLogin(); 
//			pr($ID);
			if($ID instanceof UserAccessRequest) { return $ID; }
		}
		// Check cookie incase session expired
		else if(isset($_COOKIE['hash']))
		{
//			pr('cookie');
//			pr($_COOKIE['hash']);
			$ID=$this->checkCookieLogin();
//			pr($ID);
			if($ID instanceof UserAccessRequest) { return $ID; }
		}
		
		$ID = new Guest();
		return $ID;
	}
	
	
// 		if(isset($_POST['login']['logout']) && $_POST['login']['logout'] == 'true'){}
		//prompt_login(_LOGGED_OUT_BY_REQUEST); // 'You have been successfully logged out.'
	public static function logout() 
	{
		// If the user is logged in, delete their records, otherwise just destroy their cookies and sessions.
		if(!isset($_SESSION['ID'])) { return false; }
		else if($_SESSION['ID'] instanceof Member || $_SESSION['ID'] instanceof Guest)
		{
			$ID = $_SESSION['ID'];
			$id = $ID->get('user_id');
		}
		else if(isset($_SESSION['ID']))
		{
			$ID=unserialize($_SESSION['ID']);
			$id = $ID->get('user_id'); 
		}
		else if(isset($_COOKIE['hash']))	//FIX - throwing errors from $this
		{ 
			if(isset($this))
			{	
				$this->decodeSecureCookie($_COOKIE['hash']); 
				$id = $this->unvalidated_info['id']; 
			}
		} 
		
		$last_login = $ID->getData('email');
		
		if(isset($id))
		{
			parent::MySQLUpdateAction('
				DELETE 
				FROM user_session 
				WHERE user_id=:user_id',
				array(':user_id'=>(int)$id),
				array(':user_id')
			);
		}
		
		foreach(array_keys($_COOKIE) as $key)
		{
			// thash check can be removed when we're live
			if($key != 'thash' && $key != 'chash')
			{
				setcookie($key, '', time()+60*60*24*-14, '/', COOKIE_DOMAIN); // -14 days
				unset($_COOKIE[$key]);
			}
		}
		session_unset();
		
		setcookie('last_login', $last_login, time()+60*60*24*365, '/', COOKIE_DOMAIN);
		
		//unset($_SESSION['ID']);
		return true;
	}
	
	
	
	public function refreshIDSession(){
		$ID = RuntimeInfo::instance()->idAsMember();
		$ID->loginAsUserId($ID->get('user_id'));
		$this->createSessionReference($ID->get('user_id'));
		$_SESSION['ID'] = serialize($ID);
	}
	
	private function checkCookieLogin()
	{
		$this->decodeSecureCookie($_COOKIE['hash']);
//		mail('jgilbert@thedilly.com','bug: cookie decoding successful?',serialize($this->unvalidated_info));
		$user_id=$this->unvalidated_info['id'];
		$hash_password=$this->unvalidated_info['hash_password'];
		// Check against cookie theft by compairing ips
		if($this->unvalidated_info['secure'] == true && 
		  ($this->ip != $this->unvalidated_info['ip'] || $this->proxy != $this->unvalidated_info['proxy']))
		{
 			$this->logout();
 			prompt_login(4); // ip address doesn't match the one used to log in.
			return false;
		}
		
		$Result = parent::MySQLReadAction('
			SELECT `password`, is_limited_user
			FROM user_account 
			WHERE user_id=:user_id',
			array(
				':user_id'=>$user_id
			),
			array(':user_id')
		)->getItemAt(0);
		
		// special condition for those without a password
		if($Result && $Result->getData('is_limited_user','Is::set')){
			// SUCCESS! We've reconnected now and just have to update some things
			parent::MySQLUpdateAction('
				UPDATE user_session 
				SET last_access=:last_access 
				WHERE user_id=:user_id',
				array(
					':user_id'=>(int)$user_id,
					':last_access'=>RIGHT_NOW_GMT
				),
				array(
					':user_id'
				)
			);
			$timeout=$this->createSecureCookie($user_id,null,$this->unvalidated_info['secure'],$this->unvalidated_info['idle_limit']);
			$this->createSessionReference($user_id,$timeout);
			$ID = new Member();
			$ID->loginAsUserId($user_id);
			$ID->set('Location',$this->location);
			$_SESSION['ID']=serialize($ID);
			$this->updateMembersOnline($user_id);
			return $ID;
		}
		
		//is the cookie good?
		if(isset($Result) && $Result->getData('password','Is::set') && $hash_password == $Result->get('password')) // use to be encrypted here, but changed now that encryption is in the db
		{
//			mail('jgilbert@thedilly.com','bug: cookie verification',$user_id.' '.$hash_password.' '.$this->unvalidated_info['idle_limit'].' '.$this->unvalidated_info['secure']);
			// If it is, renew it and create a session for faster acces in the future
			$timeout=$this->createSecureCookie($user_id,$hash_password,$this->unvalidated_info['secure'],$this->unvalidated_info['idle_limit']);
			$this->createSessionReference($user_id,$timeout);
			$ID = new Member();
			$ID->loginAsUserId($user_id);
			$ID->set('Location',$this->location);
			$_SESSION['ID']=serialize($ID);
			$this->updateMembersOnline($user_id);
			return $ID;
		}
		else
		{
			$this->logout();
			prompt_login(5); // Password changed.
			return false;
		}
		// return false;
	}
	
	private function checkSessionLogin()
	{
		// If the users 
		if(!isset($_SESSION['ID'])) { return false; }
		else if($_SESSION['ID'] instanceof Member || $_SESSION['ID'] instanceof Guest) { $ID = $_SESSION['ID']; }
		else { $ID = unserialize($_SESSION['ID']); }
		
		// Validate session against cached data.
		$user_id = $ID->get('user_id');
		$password = $ID->getData('password');
		
		$Result = parent::MySQLReadAction('
			SELECT `password`, unread_messages 
			FROM user_session 
			WHERE user_id=:user_id',
			array(
				':user_id'=>(int)$user_id
			),
			array(
				':user_id'
			)
		)->getItemAt(0);
		
		// special condition for those without a password
		if($ID->getData('is_limited_user','Is::set')){
//			if($Result) { $unread_messages = $Result->get('unread_messages'); }
//			else { $unread_messages = 0; }
//			$ID->setMessages($unread_messages);
			// SUCCESS! We've reconnected now and just have to update some things
			parent::MySQLUpdateAction('
				UPDATE user_session 
				SET last_access=:last_access 
				WHERE user_id=:user_id',
				array(
					':user_id'=>(int)$user_id,
					':last_access'=>RIGHT_NOW_GMT
				),
				array(
					':user_id'
				)
			);
			$this->updateMembersOnline($user_id);
			return $ID;
		}
		
		// Check to see if the record exists in the heap table
		if(isset($Result) && $Result->getData('password','Is::set'))
		{
			// Check to see if the passwords match
			if($password == $Result->get('password'))
			{
//				$ID->setMessages($Result->get('unread_messages'));
				// SUCCESS! We've reconnected now and just have to update some things
				parent::MySQLUpdateAction('
					UPDATE user_session 
					SET last_access=:last_access 
					WHERE user_id=:user_id',
					array(
						':user_id'=>(int)$user_id,
						':last_access'=>RIGHT_NOW_GMT
					),
					array(
						':user_id'
					)
				);
				
				// Session already exists, just exit!
				$this->updateMembersOnline($user_id);
				return $ID;
			}
			// If they dont match log out
			else
			{
				$this->logout();
				prompt_login(1); // password changed
				return false;
			}
		}
		// If the record expired from the db then attempt to recache it
		else
		{
			// If the account has been deleted then fail login (note: you can logout from here)
			if($this->isDead($user_id))
			{
				$this->logout();
				prompt_login(3); // dead account
				return false;
			}
			// If the account hasn't been deleted then recheck the session
			else
			{
				// The session has been terminated so authenticate via cookie.
				// Otherwise return from the login as a basic user.
				if(isset($_COOKIE['hash'])) { return $this->checkCookieLogin(); }
			}
		}
		return false;
	}
	
	public function recordLoginHistory($description, $user_id=0, $success=2) //public so registration uses it also
	{
//		$location=IP2Location::getIp2LocationDetailed($this->ip);
		if(isset($_POST['login']['l'])){
			$login = $_POST['login']['l'];
		} else if(isset($_POST['registration']['login_name'])) {
			$login = $_POST['registration']['login_name'];
		} else {
			return false;
		}
		
		UserLoginHistoryActions::insertUserLoginHistory(new UserLoginHistory(
			array(
				'user_login_unique_identifier' => $login, //$unique_identifier,
				'user_agent' => $_SERVER["HTTP_USER_AGENT"],
				'ip' => $this->ip,
				'proxy' => $this->proxy,
				'description' => $description,
				'success' => $success,
				'user_id' => $user_id
			)
		));
		
		return true;
	}
	
	public function createSessionReference($id, $abs_timeout=null)
	{
		$Result = parent::MySQLReadAction('
			SELECT `password`, unread_messages 
			FROM user_account WHERE user_id=:user_id',
			array(':user_id'=>(int)$id),
			array(':user_id')
		)->getItemAt(0);
		
		if(isset($Result) && $Result->get('password'))
		{
			if(isset($abs_timeout)){
				parent::MySQLUpdateAction('
					REPLACE INTO user_session SET 
						user_id=:user_id, 
						`password`=:password, 
						login_time=:login_time, 
						last_access=:last_access, 
						user_specified_abs_timeout=(UNIX_TIMESTAMP(NOW())+:abs_timeout),
						ip=:ip,
						proxy=:proxy,
						unread_messages=:unread_messages',
					array(
						':user_id'=>(int)$id,
						':password'=>$Result->get('password'),
						':login_time'=>RIGHT_NOW_GMT,
						':last_access'=>RIGHT_NOW_GMT,
						':abs_timeout'=>$abs_timeout,
						':ip'=>$this->ip,
						':proxy'=>$this->proxy,
						':unread_messages'=>(int)$Result->get('unread_messages')
					),
					array(':user_id',':unread_messages')
				);
			} else {
				parent::MySQLUpdateAction('
					REPLACE INTO user_session SET 
						user_id=:user_id, 
						`password`=:password, 
						login_time=:login_time, 
						last_access=:last_access, 
						ip=:ip, 
						proxy=:proxy, 
						unread_messages=:unread_messages',
					array(
						':user_id'=>(int)$id,
						':password'=>$Result->get('password'),
						':login_time'=>RIGHT_NOW_GMT,
						':last_access'=>RIGHT_NOW_GMT,
						':ip'=>$this->ip,
						':proxy'=>$this->proxy,
						':unread_messages'=>(int)$Result->get('unread_messages')
					),
					array(':user_id',':unread_messages')
				);
			}
			
			return true;
		}
		return false;
	}
	
	private function updateMembersOnline($user_id)
	{
		parent::MySQLUpdateAction('
			REPLACE INTO online_member 
			SET last_active=:last_active, 
				user_id=:user_id',
			array(
				':user_id'=>(int)$user_id,
				':last_active'=>RIGHT_NOW_GMT
			),
			array(':user_id')
		);
		
		parent::MySQLUpdateAction('
			UPDATE user_account 
			SET is_online=1 
			WHERE user_id=:user_id',
			array(
				':user_id'=>(int)$user_id
			),
			array(':user_id')
		);
		return true;
	}
	
	private function isDead($user_id)
	{
		$Result = parent::MySQLReadAction('
			SELECT is_suicide, is_killed 
			FROM user_account 
			WHERE user_id=:user_id',
			array(
				':user_id'=>(int)$user_id
			),
			array(':user_id')
		)->getItemAt(0);
		
		if(isset($Result) && $Result->getData('is_killed','Is::set'))
		{
			if($Result->getData('is_killed','Is::set'))			{ return true; }
		}
		elseif(isset($Result) && $Result->getData('is_suicide','Is::set'))
		{
			if($Result->getData('is_suicide','Is::set'))		{ return true; }
		}
		
		return false;
	}
	
	*/
}