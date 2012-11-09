<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

/////////////////////////////////////////////////////////////////////////
// No matter what this framework is doing, it has to load files to start, so preload file loading classes
//

if(!defined('SLASH')){
	if(stristr($_SERVER['SERVER_SOFTWARE'],'Win32')){ $slash = '\\'; }
	else { $slash = '/'; }
	define('SLASH',$slash);
	unset($slash);
}

// Load Startup Requirements
require_once('Core/File.php');
require_once('Core/Path.php');
require_once('Core/ConfigReader.php');
require_once('Core/SettingsRegistry.php');
require_once('Core/RuntimeInfo.php');
require_once('Core/Run.php');

//
// End file handler functions
/////////////////////////////////////////////////////////////////////////

final class Startup{
	
	use ConfigReader;
	
	////////////////////
	// Relocate these settings to an authentication class
	/*	
	private $_id;
	public function id(){
		// just doing this for code completetion purposes
		if(isset($this->_id)){ return $this->_id; }
		else { $id = new Guest(); }
		return $id;
	}
	
	public function idAsMember(){
		// just doing this for code completetion purposes
		if($this->_id instanceof Member){ return $this->_id; }
		return new Member();
	}
	
	private $_userId;
	public function userSession(){
		if(isset($this->_userId)){ return $this->_userId; }
		return new IdentifyUser();
	}
	*/
	//
	/////////////////
	
	
	public static function shutdownHandler(){
		$RuntimeInfo = RuntimeInfo::instance();
		if($RuntimeInfo instanceof Startup){
			$RuntimeInfo->__destruct();
		}
	}
	
 	// launch the application only once, but when launched, store instance in runtime info static var
 	public static function launchApplication($settings_folder_path='../../Settings', $application_name=null){
 		// constructor stores itself. no need for a return
		new Startup($settings_folder_path, $application_name);
	}

	private $_application_name;
	public function getApplicationName(){ return $this->_application_name; }
	
	private $_settings_folder_path;
	public function settingsFolderPath(){ return $this->_settings_folder_path; }
	
	private $_pageTimer;
	public function pageTimer(){ return $this->_pageTimer; }
	
	private $_connections;
	public function connections(){ return $this->_connections; }
	
	private $_helpers;
	public function helpers(){ return $this->_helpers; }
	
// 	private $_systemCache;
// 	public function systemCache(){ return $this->_systemCache; }
	
	public function __construct($settings_folder_path, $application_name){
		
		header('Content-Type: text/html; charset=UTF-8');
		
		// required first step for RuntimeInfo::instance to function
		if($application_name){ // allow for application name to be set so multiple applications can be launched simultaneously 
			$this->_application_name = $application_name;
		} else { // else, look for an existing install 
			$here = dirname(__FILE__);
			$parts = explode(SLASH, $here);
			array_pop($parts); // pop Framework off
			array_push($parts, 'Applications');
			$application_path = implode(SLASH, $parts);
			$applications = File::foldersInFolderToArray($application_path);
			if(count($applications) === 1){
				$this->_application_name = current($applications);
			} else if(count($applications) == 0) {
				// @todo Eventually this prompts you with a GUI to setup your application by clicking through a form to setup some permissions and folders and dumps out the paths to things
				die('Fatal Error: Startup was expecting an application name to load, and none were found in the Applications path.');
			} else {
				die('Fatal Error: Found multiple applications during startup, but none were specified when running Runtime::launchApplication().');
			}
			// cleanup
			unset($here, $parts, $applications, $application_path);
		}

		$this->_settings_folder_path = $settings_folder_path;
		
		// Save the instance so it can be easily recalled
		RuntimeInfo::instance($this->_application_name, $this);
		
		// this is where i need to collect the settings information from the settings folder. 
		
		SettingsRegistry::configPath($settings_folder_path);
		$settings = SettingsRegistry::get($this->_application_name.'.ini', dirname(__FILE__).'example.ini');
		$this->_config[''] = $settings;
		
		// set error handling based on debug mode in settings file
		if(isset($settings['debug']) && ($settings['debug'] != 'false' || $settings['debug'] != false)){
			if (!ini_get('display_errors')) {
				ini_set('display_errors', 1);
				error_reporting(E_ALL | E_STRICT);
			}
		}
		
		// set default timezone for date functions/objects to use
		date_default_timezone_set('UTC');
		// set internal encoding to utf8
		mb_internal_encoding('UTF-8');
		// set the string function handler to the default C style, or in the future check the availability of utf8 support by multibyte functions
		setlocale(LC_CTYPE, 'C'); 
				
		// include all files required by the startup process
		$this->includeRequirements();
		
// 		if($this->config(null,null)->getBoolean('online')){
// 			echo 'Site is online';
// 		} else {
// 			echo 'Site is offline';
// 		}
		
		// initialize connections container
		$this->_connections = new Connections();
		
		// initialize helpers container
		$this->_helpers = new Helpers();
		
		// this is test code for connections 
		
// 		$query='SELECT NOW() AS henceforth';
// 		$master = $this->connections()->MySQL('master');
// 		$slave1 = $this->connections()->MySQL('slave1');
// 		$sqlite = $this->connections()->SQLite();
		
// 		$master->query($query);
// 		$master->read();
// 		pr($master->all_data);
		
// 		$query='SELECT * FROM item_category WHERE item_category_id > :item_category_id';
// 		$slave1->prepare($query);
// 		$slave1->execute(array(':item_category_id'=>18),array(':item_category_id'));
// 		$slave1->read();
// 		pr($slave1->all_data);
		
// 		$query='SELECT date("now") AS henceforth';
// 		$sqlite->query($query);
// 		$sqlite->read();
// 		pr($sqlite->all_data);
		
		//$this->connections()->Postmark();
		
		//$this->connections()->AmazonS3();
		//phpinfo();die;
		
// 		try {
// 			// Initialize
// 			$cache = $this->connections()->Memcached();
			
// 			// Create test resource
// 			$resource = new stdClass();
// 			$resource->name = 'Test';
		
// 			// Attempt to retrieve previously cached result from pool (run this twice)
			
// 			$key = 'TestingMemcache';
			
// 			if(!$cache->get($key)) {
// 				print "Key was not found in our cache pool!\n";
		
		
// 				// If nothing was found during our cache look up, save resource to cache pool
// 				if ($cache->set($key, $resource)) {
// 					print "Stored resource in cache pool!\n";
// 				} else {
// 					print "Failed to store resource in cache pool!\n";
// 				}
// 			} else {
// 				print "Key was found in our cache pool!\n";
		
// 				// We retrieved resource from cache, let's make sure delete works
// 				if ($cache->delete($key)) {
// 					print "Deleted resource from cache!\n";
// 				} else {
// 					print "Failed to delete resource from cache!\n";
// 				}
// 			}
		
// 			print "Resource: \n";
		
// 			print_r($resource);
		
// 		} catch (Exception $exception) {
// 			echo "Error happened during memcache startup: ";
// 			print $exception->getMessage();
// 		}
		
// 		$instagram = $this->connections()->Instagram();
		
// 		echo "<a href='{$instagram->getLoginUrl()}'>Login with Instagram</a>";
		
// 		pr($instagram);

		// now test code for helpers
		
// 		$ImageHelper = $this->helpers()->Image();
// 		$ImageHelper->load('http://static.php.net/www.php.net/images/php.gif')->flip(ImageAbstraction::FLIP_HORIZONAL)->resize(100, 100)->show('jpg',70);
		
//		$VideoHelper = $this->helpers()->Video()->load('path')->saveAsH264('outputpath.m4v');

		//$this->helpers()->Session()->flushMessages();
		
		// start the official page time after majority of startup tasks have been completed
		$this->_pageTimer = new Timer();
		$this->_pageTimer->start();
//		$this->_systemCache = new CacheObjectCollection();
		register_shutdown_function('Startup::shutdownHandler');
	}
	
	public function __destruct(){
		// 
	}
	
	private function includeRequirements(){
		
		// Include requirements for running MVC
		$files = File::filesInFolderToArray(Path::toFramework().'Structure/');
		foreach($files as $path => $file){
			require_once $path;
		}
		
		// Load dependencies for addons
		$files = File::filesInFolderToArray(Path::toFramework().'AddOns/');
		foreach($files as $path => $file){
			require_once $path;
		}
		
		// Load add ons
		$files = File::filesInFolderToArray(Path::toAddOns().'Formats/');
		foreach($files as $path => $file){
			require_once $path;
		}
		
		// Load the connectors container. It can load all the dependencies lazy load style
		Run::fromConnections('Connections.php');
		
		// Same for helpers
		Run::fromHelpers('Helpers.php');
		
		// Load the Models customization layer
		Run::fromModels('DataObject.php');
		Run::fromModels('DataCollection.php');
		
		// Load application requirements
		$files = File::filesInFolderToArray(Path::toModels($this->_application_name).'Required/');
		foreach($files as $path => $file){
			require_once($path);
		}
		
		// Load remainder of framework requirements which can now inherit developer level changes to object base, customized outputs, etc
		$files = File::filesInFolderToArray(Path::toFramework().'Requirements/');
		foreach($files as $path => $file){
			require_once($path);
		}
		
		Run::fromControllers('PageController.php');
	}
	
	// This should be sensitive to the type of output the page should be rendering, but currently isn't
	public function handlePageRequest(){
		// This is a multilingual framework which supports utf8 as its base. might need to consider if this breaks images though.
		
// 		$ModelTest = new DataObject(array('utf8-text'=>file_get_contents(dirname(__FILE__).'/utf8.txt')));
// 		echo $ModelTest->getString('utf8-text');
// 		die;
		
		$app_path = Path::toApplication();
		$controller_path = Path::toControllers();
		
		if(!isset($_GET['framework']) || !is_array($_GET['framework'])){
			Run::fromControllers('Pages/IndexPage.php');
			$Page = new IndexPage();
		} else {
			$f = $_GET['framework'];
			
			if(isset($f['controller_name']) && $f['controller_name'] == ''){
				Run::fromControllers('Pages/IndexPage.php');
				$Page = new IndexPage();
			} else if(isset($f['controller_name'])) {
				$controller_name = preg_replace('/([^a-zA-Z0-9])/s','',$f['controller_name']);
				if(isset($f['folder_name'])){
					$folder_name = preg_replace('/([^a-zA-Z0-9])/s','',$f['folder_name']);
					if(file_exists(File::osPath($controller_path.'Pages/'.$folder_name.'/'.$controller_name.'Page.php'))){
						Run::fromControllers('Pages/'.$folder_name.'/'.$controller_name.'Page.php');
						if(class_exists($controller_name.'Page')){
							$class_name = $controller_name.'Page';
							$Page = new $class_name();
						} else {
							Run::fromControllers('Pages/Err404Page.php');
							$Page = new Err404Page();
						}
					} else {
						Run::fromControllers('Pages/Err404Page.php');
						$Page = new Err404Page();
					}
				} else if(file_exists(File::osPath($controller_path.'Pages/'.$controller_name.'Page.php'))){
					Run::fromControllers('Pages/'.$controller_name.'Page.php');
					if(class_exists($controller_name.'Page')){
						$class_name = $controller_name.'Page';
						$Page = new $class_name();
					} else {
						Run::fromControllers('Pages/Err404Page.php');
						$Page = new Err404Page();
					}
				} else {
					Run::fromControllers('Pages/Err404Page.php');
					$Page = new Err404Page();
				}
			} else {
				Run::fromControllers('Pages/IndexPage.php');
				$Page = new IndexPage();
			}
		}
		
		$not_rendered = true;
		if(isset($f['controller_format']) && (strtolower($f['controller_format']) != 'page' || strtolower($f['controller_format']) != 'html')){ //  && $f['controller_format'] != 'html' // for optional link formatting
			$output_method = 'render'.$f['controller_format'];
			if(method_exists($Page,$output_method)){
				$Page->$output_method();
				$not_rendered = false;
			}
		}
		
		if($not_rendered) {
			if($Page instanceof PageController){
				$Page->renderPage();
				$Page->renderHtmlPage();
			} else if($Page instanceof PageController){
				$Page->renderPage();
			}
		}
		echo $Page->getOutput();
	}
}


/*
 * depricated code
 * 
 		// start session support /////// this is going to work through a lazy loader
//		$this->initializeSessions();
		
		// get the site config before logging in so we know if users even allowed to log in ///////// this needs to happen from the ini not the db
//		$this->initializeSiteConfig();
		
		// test the various authentication types (sessions, cookies, forms, apis) //////////// this also needs to be initialized outside of the framework startup by the controller. logins shouldnt always be required
//		$this->authenticateUser(); 

	private function initializeSessions(){ // sessions shouldnt be initialized until they're needed / called through the handler
		// If this is a bot, don't start a session
		if((BrowserDetection::detectBrowser('type') === 'bot') === false){
			// start the session handler
			Sessions::initialize();
		
			// @TODO this use to have functionality to prevent session stagnation which is a potential security risk. changing the session id every request helps prevent hackers from hijacking sessions. right now, though, all this does is create a ton of duplicate sessions
			
			// do not delete the session here. it will happen at the closing of the file
// 			$tmp = $_SESSION;
// 			define('OLD_SESSION_ID', session_id());
// 			session_regenerate_id(); // destroy the session in the destructor so we can count guests 
// 			define('CURRENT_SESSION_ID', session_id());
// 			$_SESSION = $tmp;
		}	
	}
	
	private function authenticateUser(){
//		global $ID, $UserId;
		
		$db = $this->mysql();
		
		if(isset($_REQUEST['API_USR_KEY']) && isset($_REQUEST['API_APP_KEY'])){
			$this->_userId = $UserId = new IdentifyApplication();
		} else {
			$this->_userId = $UserId = new IdentifyUser();
		}
		
		$this->_id = $ID = $UserId->loginCheck();
		
		// if(isset($_SESSION['ComMgr']))
		// {
		// 	$ComMgr=unserialize($_SESSION['ComMgr']);
		// 	if(isset($_POST['community_manager']['logout']))
		// 	{
		// 		$ComMgr->logout();
		// 		$ComMgr = new CommunityManager();
		// 	}
		// }
		// else { $ComMgr = new CommunityManager(); }
		
		if($ID->isOnline())
		{
			// For sessions debugging only
			$_my_id_=$ID->get('user_id');
			
			$my_history=HistoryActions::traceUser($ID->get('user_id'), 0, 10);
		}

		if(isset($_GET['framework']) && isset($_GET['framework']['folder_name']) && !empty($_GET['framework']['folder_name'])){
			$remote_request = $_SERVER['PHP_SELF'].'?folder_name='.$_GET['framework']['folder_name'].'&controller_name='.$_GET['framework']['controller_name'];
		} else if(isset($_GET['framework']) && isset($_GET['framework']['controller_name']) && !empty($_GET['framework']['controller_name'])) {
			$remote_request = $_SERVER['PHP_SELF'].'?controller_name='.$_GET['framework']['controller_name'];
		} else {
			$remote_request = $_SERVER['PHP_SELF'];
		}
		
		// this all needs to be moved into initialization handler that developers can author themselves. inserting page hits is not a core requirement for startup
		// page stats 
// 		if($ID->isOnline()) 										 { PageHitActions::insertMemberHit($remote_request); }
// 		else if((BrowserDetection::detectBrowser('type') === 'bot')) { PageHitActions::insertBotHit($remote_request); }
// 		else 														 { PageHitActions::insertGuestHit($remote_request); }
//		echo "\n\n<br><br>";
	}
 
 */
