<?php

class CloseComponentController extends ComponentController{
	
	protected function loadIncludedFiles(){
		// Ex: $this->loadModels(array('Close'));
		// Ex: $this->loadActions(array('CloseActions'));
		/* page dependencies */
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected function handleRequest(){
		/* business logic */
	}
	
	public function renderPage(){
		$this->addCss('pages/Account/Close');
		$this->addJs('pages/Account/Close');
		
		/* page logic */
		
		/*
		// Use HTML templates for widgets shared by javascript ui and php ui
		$this->_tpl->load('pages/Account/Close/layout.htm');
		$this->_page_body = $this->_tpl->parse(array(
			'BR'=>BR // array of key value pairs for replacing variables in the HTML template
		));
		*/
		
		// or if you're running a page that uses PHP instead of HTML templates, run it this way
		$this->_page_body = $this->runCodeReturnOutput('pages/Account/Close/layout.php');
	}
}