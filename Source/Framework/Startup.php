<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

/////////////////////////////////////////////////////////////////////////
// No matter what this framework is doing, it has to load files to start, so preload file loading classes
//

if(!defined('SLASH')){
	// it seems foolish to only check for win32 or win64
	if(stristr($_SERVER['SERVER_SOFTWARE'],'Win32') || stristr($_SERVER['SERVER_SOFTWARE'],'Win64')){ $slash = '\\'; }
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
	
	public static function cast(Startup $Startup){ return $Startup; }
	
	public static function shutdownHandler(){
		$RuntimeInfo = RuntimeInfo::instance();
		if($RuntimeInfo instanceof Startup){
			$RuntimeInfo->__destruct();
		}
	}
	
 	// launch the application only once, but when launched, store instance in runtime info static var
 	public static function launchApplication($settings_folder_path='../../Settings', $application_name, $web_root){
 		// constructor stores itself. no need for a return
		new Startup($settings_folder_path, $application_name, $web_root);
	}

	private $_application_name;
	public function getApplicationName(){ return $this->_application_name; }
	
	private $_settings_folder_path;
	public function settingsFolderPath(){ return $this->_settings_folder_path; }
	
	private $_web_root;
	public function getWebRoot(){ return $this->_web_root; }
	
	private $_now;
	public function now(){ return $this->_now; }
	
	private $_debug;
	public function isDebugModeOn(){ return (isset($this->_debug)?(bool)$this->_debug:true); }
	
	private $_pageTimer;
	// @deprecated 
	public function pageTimer(){ return $this->_pageTimer; }
	public function getPageTimer(){ return $this->_pageTimer; }
	
	private $_connections;
	// @deprecated 
	public function connections(){ return $this->_connections; }
	public function getConnections(){ return $this->_connections; }
	
	private $_helpers;
	// @deprecated 
	public function helpers(){ return $this->_helpers; }
	public function getHelpers(){ return $this->_helpers; }
	
	private $_pathing_info;
	public function getControllerName(){ return isset($this->_pathing_info['controller_name'])?$this->_pathing_info['controller_name']:'Index'; }
	public function getControllerPath(){ return isset($this->_pathing_info['controller_path'])?$this->_pathing_info['controller_path']:''; }
	public function getControllerFormat(){ return isset($this->_pathing_info['controller_format'])?$this->_pathing_info['controller_format']:'html'; }
	public function getComponentName(){ return isset($this->_pathing_info['component_name'])?$this->_pathing_info['component_name']:''; }
	public function getComponentControllerName(){ return isset($this->_pathing_info['component_controller_name'])?$this->_pathing_info['component_controller_name']:''; }
	public function getComponentControllerPath(){ return isset($this->_pathing_info['component_controller_path'])?$this->_pathing_info['component_controller_path']:''; }
	
	public function appSettings(){
		static $appSettings = null;
		return (!is_null($appSettings))?$appSettings:new DataObject(current($this->_config));
	}
	
// 	private $_systemCache;
// 	public function systemCache(){ return $this->_systemCache; }
	
	private function __construct($settings_folder_path, $application_name, $web_root){
		
		// Why is this the default? For error catching? 
		header('Content-Type: text/html; charset=UTF-8');
		
		$this->_application_name = $application_name;
		$this->_settings_folder_path = $settings_folder_path;
		$this->_web_root = $web_root;
		
		// Save the instance so it can be easily recalled
		RuntimeInfo::instance($this);
		
		// this is where i need to collect the settings information from the settings folder. 
		
		try{
			SettingsRegistry::configPath($settings_folder_path);
			$settings = SettingsRegistry::get($this->_application_name.'.ini', dirname(__FILE__).'/example.ini');
		} catch(Exception $e){
			echo $e->getMessage();
			die;
		}
			
		$this->_config[''] = $settings;
		
		// Set XML errors to not be thrown by xml in scripts, but instead be readable by error handling functions like libxml_get_errors. 
		// This happens here because if it were to happen within a script, the docs say it can reset error handlers 
		// (for instance, if i added an error handler to this framework it would no longer work if i ran this later in the scripts)
		// http://php.net/manual/en/function.libxml-use-internal-errors.php
		libxml_use_internal_errors();
		
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
		
 		$this->_debug = isset($settings['']['debug'])?$settings['']['debug']:false;
		
// 		if($this->config(null,null)->getBoolean('online')){
// 			echo 'Site is online';
// 		} else {
// 			echo 'Site is offline';
// 		}
		
		// initialize connections container
		$this->_connections = new Connections();
		
		// initialize helpers container
		$this->_helpers = new Helpers();
		
		// start the official page time after majority of startup tasks have been completed
		$this->_pageTimer = new Timer();
		$this->_pageTimer->start();
		
//		$this->_systemCache = new CacheObjectCollection();

		$this->_now = new DateTimeObject('now');
		
		register_shutdown_function('Startup::shutdownHandler');
	}
	
	public function __destruct(){
		// 
	}
	
	public static function autoloadModel($class_name) {
		$collection_check = mb_substr($class_name, -mb_strlen('Collection'));
		if($collection_check == 'Collection'){ $class_name = mb_substr($class_name, 0, -mb_strlen('Collection')); }
		
		$filename = 'Custom/'.$class_name.'.php';
	    if (File::exists(Path::toModels().$filename)) {
	    	Run::fromModels('Custom/'.$class_name.'.php');
	    }
	}
	
	public static function autoloadAction($class_name) {
		$filename = 'Custom/'.$class_name.'.php';
	    if (File::exists(Path::toActions().$filename)) {
	    	Run::fromActions('Custom/'.$class_name.'.php');
	    }
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
		
		// Load Component fetcher after all other dependencies have been loaded.
		Run::fromComponents('Components.php');
		
		// Load the Models customization layer
		Run::fromModels('DataObject.php');
		Run::fromModels('DataCollection.php');
		
		spl_autoload_register('StartUp::autoloadModel');
		spl_autoload_register('StartUp::autoloadAction');
		
		// Load the sugar methods in actions so the actions accessors can better format the output to DataObjects and DataCollections
		Run::fromActions('Actions.php');
		
		// Base classes that others inherit from 
		Run::fromFramework('AddOns/Validation/Validators/ValidationRule.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidString.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidNumber.php');
		
		// Basic rules for forms to follow
		Run::fromFramework('AddOns/Validation/Validators/ValidColor.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidCreditCard.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidCurrency.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidDate.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidEmail.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidInteger.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidIP.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidPhoneNumber.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidPostalCode.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidSocialSecurityNumber.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidTime.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidURL.php');
		Run::fromFramework('AddOns/Validation/Validators/ValidWords.php');
		
		// Load Validator class and the extended class 
		Run::fromFramework('AddOns/Validation/Validator.php');
		
		// Reference your custom rules in this empty FormValidator class
		Run::fromFormValidators('FormValidator.php');
		
		FormValidator::loadCustomValidators();
		
		// Load the PageController which is the base of all page requests and allows the user to override controller behaviors for things like themes, logins, etc
		Run::fromControllers('PageController.php');
		Run::fromControllers('ComponentController.php');
	}
	
	// This should be sensitive to the type of output the page should be rendering, but currently isn't
	public function handlePageRequest(){
		// This is a multilingual framework which supports utf8 as its base. might need to consider if this breaks images though.
		
		$app_path = Path::toApplication();
		$controller_path = Path::toControllers();
		
		if(isset($_GET['framework']) && isset($_GET['framework']['requested_url'])){
			$MinnowRequest = new MinnowRequest($_GET['framework']['requested_url']);
		} else {
			$MinnowRequest = new MinnowRequest();
		}
		
		$this->_pathing_info = $MinnowRequest->getPathInfoAsArray();
		
		if($MinnowRequest->hasControllerFile()) {
			// Do controller logic
			Run::fromControllers($MinnowRequest->getPathToControllerFile(false));
			if(class_exists($MinnowRequest->getControllerName().'Page')){
				$class_name = $MinnowRequest->getControllerName().'Page'; 
				$Page = new $class_name;
				
				if(!is_a($Page, 'PageController')){
					// Class isn't a page controller
					Run::fromControllers('Pages/Err404Page.php');
					$Page = new Err404Page();
				}
			} else {
				// Missing class object
				Run::fromControllers('Pages/Err404Page.php');
				$Page = new Err404Page();
			}
		} else {
			// Do 404 logic
			Run::fromControllers('Pages/Err404Page.php');
			$Page = new Err404Page();
		}
		
		if(strtolower($MinnowRequest->getControllerFormat()) == 'html' && is_a($Page, $MinnowRequest->getControllerFormat().'Capable')){
			$Page->renderHTML();
			$Page->renderThemedHTMLPage();
		} else if(is_a($Page, $MinnowRequest->getControllerFormat().'Capable')){ // check to see if the page is capable of rendering this content
			$output_method = 'render'.$MinnowRequest->getControllerFormat();
			$Page->$output_method();
		} else { // if not supported, error 
			header('HTTP/1.0 404 Not Found');
			header('Status: 404 Not Found');
			echo 404;
		}
		
		echo $Page->getOutput(); // output whats in the page buffer
	}
}
