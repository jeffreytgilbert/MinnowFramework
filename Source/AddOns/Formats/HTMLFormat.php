<?php

interface HTMLCapable{
	public function renderHTML();
}

trait HTMLFormat{
	
	protected $_page_title = '';
	protected $_page_keywords='';
	protected $_page_description='';
	protected $_page_author='';
	protected $_extra_js=array();
	protected $_extra_css=array();
	protected $_remote_js=array();
	protected $_remote_css=array();
	protected $output_template = '';
	
	public function initializeHTML(){
		$Page = PageController::cast($this);
		
		$this->_page_title = $Page->getAppSettings()->getString('default_page_title');
		$this->_page_keywords = $Page->getAppSettings()->getString('default_page_keywords');
		$this->_page_description = $Page->getAppSettings()->getString('default_page_description');
		$this->_page_author = $Page->getAppSettings()->getString('default_page_author');
	}
	
	/*
	 * @depricated
	 */
	public function renderPage(){
		self::renderHTML();
	}
	
	public function renderHTML(){
		$Page = PageController::cast($this);
		$path = ($Page->getControllerPath() == '')?$Page->getControllerName():$Page->getControllerPath().'/'.$Page->getControllerName();
		$this->addCss('Pages/'.$path);
		$this->addJs('Pages/'.$path);
		
		$this->_page_body = $this->runCodeReturnOutput('Pages/'.$path.'/layout.php');
	}
	
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
