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
		if($this instanceof PageController){
			$Page = PageController::cast($this);
		} else {
			$Page = ComponentController::cast($this);
		}
		
		$PageDetails = SitemapActions::selectByURL($this->getControllerPath().$this->getControllerName());
			
		$this->_page_title = $PageDetails->getString('title') == ''
			?$Page->getAppSettings()->getString('default_page_title')
			:$PageDetails->getStringAsHTMLEntities('title');
		$this->_page_description = $PageDetails->getString('description') == ''
			?$Page->getAppSettings()->getString('default_page_description')
			:$PageDetails->getStringAsHTMLEntities('description');
	}
	
	public function renderHTML(){
		$Page = PageController::cast($this);
		$path = ($Page->getControllerPath() == '')?$Page->getControllerName():$Page->getControllerPath().$Page->getControllerName();
		$this->addCss('Pages/'.$path);
		$this->addJs('Pages/'.$path);
		
		$this->_page_body = $Page->runCodeReturnOutput('Pages/'.$path.'/layout.php');
		return $this->_page_body;
	}
	
	public function setPageTitle($page_title){ $this->_page_title = $page_title; }
	public function getPageTitle(){ return $this->_page_title; }
	
	public function setPageDescription($page_description){ $this->_page_description[] = $page_description; }
	public function getPageDescription(){ return $this->_page_description; }
	
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

	public function disableCache() {
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");
	}
}
