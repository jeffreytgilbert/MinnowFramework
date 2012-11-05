<?php

abstract class PageRequest extends Controller{
	protected $_tpl;
	
	protected $_page_title = '';
	protected $_page_keywords='';
	protected $_page_description='';
	protected $_page_author='';
	protected $_extra_js=array();
	protected $_extra_css=array();
	protected $_remote_js=array();
	protected $_remote_css=array();
	protected $output_template = '';
	
	// All ok.
	public $SystemNotifications;
	
	public function __construct(){
		$this->SystemNotifications = new DataObject();	// store confirmations of an action
		$this->_tpl = new TemplateParser();
		
		parent::__construct();
		$this->loadIncludedFiles();
		
		$script = isset($_GET['framework']['script_name'])?$_GET['framework']['script_name']:null;
		$format = isset($_GET['framework']['output_format'])?$_GET['framework']['output_format']:null;
		
		// before any other page logic hits, make sure registration requirements have been met
		$ID = RuntimeInfo::instance()->id();
		if($ID->isOnline()){
			$MyID = RuntimeInfo::instance()->idAsMember();
			if($MyID->isRole(Role::_CHILD)){
				if($MyID->getAvatarPartCollection()->length() == 0 && 
					!in($script,array('Avatar','Logout'))){
					if($format == 'JSON'){
						$this->Errors->set('AVATAR_REQUIRED','Please save an avatar before continuing.');
					} else {
						$this->redirect('/My/Avatar/?required=1',200,true);
					}
				} else if($MyID->getTheme()->length() == 0 && 
					!in($script,array('Avatar','Theme','Logout'))) {
					if($format == 'JSON'){
						$this->Errors->set('THEME_REQUIRED','Please save a theme before continuing.');
					} else {
						$this->redirect('/My/Theme/?required=1',200,true);
					}
				}
			} else if($MyID->isRole(Role::_PARENT)){
				if( !$MyID->getData('is_email_validated','Is::set') && 
					!in($script,array(
						'PrimaryEmailAddress',
						'PaypalEmailAddress',
						'LinkUpApprove',
						'Verify',
						'BillingInfo',
						'ShippingInfo',
						'Dashboard', // this ones on here for the messages received after confirming email validations
						'Account',
						'Logout'
					))){
					if($format == 'JSON'){
						$this->Errors->set('VALIDATE_EMAIL_REQUIRED','Please validate your email address before continuing.');
					} else {
						$this->redirect('/My/Account/?required=VALIDATE_EMAIL_REQUIRED',200,true);
					}
				} else if($MyID->getProfile()->length() == 0 && 
					!in($script,array(
						'PrimaryEmailAddress',
						'PaypalEmailAddress',
						'LinkUpApprove',
						'Verify',
						'BillingInfo',
						'ShippingInfo',
						'Dashboard', // this ones on here for the messages received after confirming email validations
						'Account',
						'PaypalPurchaseReturn',
						'Logout'
					))){
					if($format == 'JSON'){
						$this->Errors->set('VALID_SHIPPING_ADDRESS_REQUIRED','Please set the address you want items to be shipped to before continuing.');
					} else {
						$this->redirect('/My/Account/?required=VALID_SHIPPING_ADDRESS_REQUIRED',200,true);
					}
				} else if(!$MyID->boolean('is_account_cc_validated') && 
					!in($script,array(
						'PrimaryEmailAddress',
						'PaypalEmailAddress',
						'LinkUpApprove',
						'Verify',
						'BillingInfo',
						'ShippingInfo',
						'Dashboard', // this ones on here for the messages received after confirming email validations
						'Account',
						'PaypalPurchaseReturn',
						'Logout'
				))){
					if($format == 'JSON'){
						$this->Errors->set('VALID_BILLING_INFO_REQUIRED','Please validate your account with a PayPal account before continuing.');
					} else {
						$this->redirect('/My/Account/?required=VALID_BILLING_INFO_REQUIRED',200,true);
					}
				}
			}
		}
		
		$this->handleRequest();
//		$this->renderPage();
	}
	
	abstract public function renderPage();
	
	public function setPageTitle($page_title){ $this->_page_title = $page_title; }
	public function getPageTitle(){ return $this->_page_title; }
	
	public function setPageKeywords($page_keywords){ $this->_page_keywords[] = $page_keywords; }
	public function getPageKeywords(){ return $this->_page_keywords; }
	
	public function setPageDescription($page_description){ $this->_page_description[] = $page_description; }
	public function getPageDescription(){ return $this->_page_description; }
	
	public function setPageAuthor($page_author){ $this->_page_author[] = $page_author; }
	public function getPageAuthor(){ return $this->_page_author; }
	
	public function addCss($css, $override_previous_include=false){
		if($override_previous_include || !in_array($css,$this->_extra_css)){
			$this->_extra_css[] = $css;
		}
	}
	public function getCss(){ return $this->_extra_css; }
	
	public function addJs($js, $override_previous_include=false){
		if($override_previous_include || !in_array($js,$this->_extra_js)){
			$this->_extra_js[] = $js;
		}
	}
	public function getJs(){ return $this->_extra_js; }
	
	public function addRemoteCss($css, $override_previous_include=false){
		if($override_previous_include || !in_array($css,$this->_remote_css)){
			$this->_remote_css[] = $css;
		}
	}
	public function getRemoteCss(){ return $this->_remote_css; }
	
	public function addRemoteJs($js, $override_previous_include=false){
		if($override_previous_include || !in_array($js,$this->_remote_js)){
			$this->_remote_js[] = $js;
		}
	}
	public function getRemoteJs(){ return $this->_remote_js; }
	
	public function getTemplateEngine(){ return $this->_tpl; }

	public function redirect($url, $status = null, $exit = true){
		if (!empty($status)) {
			$codes = array(
				100 => 'Continue',
				101 => 'Switching Protocols',
				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',
				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				307 => 'Temporary Redirect',
				400 => 'Bad Request',
				401 => 'Unauthorized',
				402 => 'Payment Required',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Time-out',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Large',
				415 => 'Unsupported Media Type',
				416 => 'Requested range not satisfiable',
				417 => 'Expectation Failed',
				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Time-out'
			);
			if (is_string($status)) {
				$codes = array_combine(array_values($codes), array_keys($codes));
			}

			if (isset($codes[$status])) {
				$code = $msg = $codes[$status];
				if (is_numeric($status)) {
					$code = $status;
				}
				if (is_string($status)) {
					$msg = $status;
				}
				$status = "HTTP/1.1 {$code} {$msg}";
			} else {
				$status = null;
			}
		}

		if (!empty($status)) {
			header($status);
		}
		
		if ($url !== null) {
			header('Location: '.$url);
		}

		if (!empty($status) && ($status >= 300 && $status < 400)) {
			header($status);
		}

		if ($exit) {
			if (function_exists('session_write_close')) {
				session_write_close();
			}
			exit;
		}
	}

	public function disableCache() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
}
