<?php

/*
 * the controller you define as part of your application customization (the one all requests inherit from) loads the trait, 
 * however all that does is give you methods you CAN call... now, the page, when it loads, will check to see if the page you're 
 * writing IMPLEMENTS an interface of say HTMLCapable and if so, then it will check to see if those things DEFINED in the interface 
 * are there (which they dont have to be there if not defined by the trait, and if they are, then render the page in that format, 
 * and if not, ignore that request type and go to default
 */

abstract class PageController extends Controller{
	
	use HTMLFormat, JSONFormat, XMLFormat;
	
	// add some constants for commonly used pages
	const _LOGIN_PAGE = '/Account/Login/';
	const _LOGOUT_PAGE = '/Account/Logout/';
	const _REGISTRATION_PAGE = '/Account/Login/';
	const _404_PAGE = '/404/'; // couldnt find content
	const _403_PAGE = '/403/'; // unexpected login error
	const _500_PAGE = '/500/'; // server error
	
	protected $_Session, $_Authentication, $_Components;
	
	public function getSession(){ return SessionAbstraction::cast($this->_Session); }
	public function getAuthentication(){ return AuthenticationComponent::cast($this->_Authentication); }
	public function getComponents(){ return Components::cast($this->_Components); }
	
	// define the logic that happens on every page for your application
	
	protected $_ID;
	public function getID(){
		if(isset($this->_ID) && $this->_ID instanceof AccessRequest){ return $this->_ID; }
		$this->_ID = $this->getAuthentication()->identifyUser();
		return $this->_ID;
	}
	
	// shorthand methods for saving messages to sessions for page redirects after form submissions
	public function flashError($error_name, $message){
		$this->getSession()->setError($message, $error_name);
	}
	
	public function flashNotice($notice_name, $message){
		$this->getSession()->setNotice($message, $notice_name);
	}
	
	public function flashConfirmation($confirmation_name, $message){
		$this->getSession()->setConfirmation($message, $confirmation_name);
	}
	
	public function __construct($ParentObject=null){
		parent::__construct($ParentObject);
		
		// now that everything has been initialized, set the time to the mysql servers time since that's the connection we're using.
		$MasterConnection = $this->getConnections()->MySQL();
		$MasterConnection->query('SELECT NOW() AS right_now_gmt');
		$MasterConnection->readRow();
		RuntimeInfo::instance()->now()->setTimestamp(strtotime($MasterConnection->row_data['right_now_gmt']));
		
		// Load sessions straight away because anything afterwards needs to use the db session handler
		$this->getHelpers()->Session()->start(); // especially sessions, since it needs to run before anything else starts a session
		
		$this->_Session = $this->getHelpers()->Session();
		
		// load required files for this controller automatically and do so before components so components can use included files
		$this->loadIncludedFiles();
		
		// load components needed on every page manually. These may have object dependencies / inheritance issues if auto loaded
// 		Run::fromComponents('AuthenticationComponent.php');
		
		$this->_Components = new Components();
		
		$this->_Authentication = $this->_Components->Authentication($this);
		
		// add all the javascript files you want loaded every html page request here
		$this->_extra_js = array_merge($this->_extra_js,array(
			// Before editing this, make sure you can't just change the StartUp.js file to load your requirements for the template
		));
		
		// add all the css files you want loaded every html page request here
		$this->_extra_css = array_merge($this->_extra_css,array(
			'default'
		));
		
		$this->handleRequest();
		
		$message_types = $this->getSession()->flushMessages();
		foreach($message_types as $message_type => $messages){
			$message_method = 'get'.$message_type;
			if(is_array($messages) && count($messages)>0){
				foreach($messages as $message_code => $message){
// 					$this->getConfirmations()->set($message_code,$message);
					$this->$message_method()->set($message_code,$message);
				}
			}
		}
	}
	
	public function renderThemedHTMLPage(){
		header('Content-Type: text/html; charset=UTF-8');
		
		$this->_output = $this->runCodeReturnOutput('Themes/desktop.php');
	}
	
	public static function cast(PageController $PageController){
		return $PageController;
	}
}
