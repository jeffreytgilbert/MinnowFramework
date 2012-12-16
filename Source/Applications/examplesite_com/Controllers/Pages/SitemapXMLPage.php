<?php

class SitemapXMLPage extends PageController implements HTMLCapable{
	protected function loadIncludedFiles(){
		// Ex: $this->loadModels(array(''));
		// Ex: $this->loadActions(array(''));
		/* page dependencies */
	}
	
	public function handleRequest(){
		/* business logic */
	}
	
	public function renderHTML(){
		require(dirname(__FILE__).'/../Views/pages/SitemapXML/layout.php');
		exit;
	}
}