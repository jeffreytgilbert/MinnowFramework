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
	
	protected $Session;
	
	// define the logic that happens on every page for your application
	
	public function __construct(){
		parent::__construct();
		
		// now that everything has been initialized, set the time to the mysql servers time since that's the connection we're using.
		$MasterConnection = $this->getConnections()->MySQL();
		$MasterConnection->query('SELECT NOW() AS right_now_gmt');
		$MasterConnection->readRow();
		RuntimeInfo::instance()->now()->setTimestamp(strtotime($MasterConnection->row_data['right_now_gmt']));
		
		// load components needed on every page manually. These may have object dependencies / inheritance issues if auto loaded
		Run::fromComponents('AuthenticationComponent.php');
		
		$this->loadIncludedFiles();
		
		// add all the javascript files you want loaded every html page request here
		$this->_extra_js = array_merge($this->_extra_js,array(
			'default'
		));
		
		// add all the css files you want loaded every html page request here
		$this->_extra_css = array_merge($this->_extra_css,array(
			'default'
		));
		
		$this->Session = $this->getHelpers()->Session();
		
		$HybridAuth = $this->getHelpers()->HybridAuth();
		
		$this->handleRequest();
	}
	
	public function renderThemedHTMLPage(){
		header('Content-Type: text/html; charset=UTF-8');
		
		$this->_output = $this->runCodeReturnOutput('Themes/desktop.php');
	}
	
	public static function cast(PageController $PageController){
		return $PageController;
	}
}
