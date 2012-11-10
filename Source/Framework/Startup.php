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
	
	public static function cast(Startup $Startup){ return $Startup; }
	
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
	
	public function appSettings(){ return current($this->_config); }
	
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
		
		// Load the sugar methods in actions so the actions accessors can better format the output to DataObjects and DataCollections
		Run::fromActions('Actions.php');
		
		// Load the PageController which is the base of all page requests and allows the user to override controller behaviors for things like themes, logins, etc
		Run::fromControllers('PageController.php');
	}
	
	// This should be sensitive to the type of output the page should be rendering, but currently isn't
	public function handlePageRequest(){
		// This is a multilingual framework which supports utf8 as its base. might need to consider if this breaks images though.
		
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
		if(isset($f['controller_format']) && strtolower($f['controller_format']) != 'page' && strtolower($f['controller_format']) != 'html'){ //  && $f['controller_format'] != 'html' // for optional link formatting
			$output_method = 'render'.$f['controller_format'];
			if(method_exists($Page,$output_method)){
				$Page->$output_method();
				$not_rendered = false;
			}
		}
		
		if($not_rendered) {
			if($Page instanceof HTMLCapable){
				$Page->renderHTML();
				$Page->renderThemedHTMLPage();
			}
		}
		echo $Page->getOutput();
	}
}
