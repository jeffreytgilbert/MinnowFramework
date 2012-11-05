<?php

class SitemapXMLPage extends PageRequest{
	protected function loadIncludedFiles(){
		// Ex: $this->loadModels(array(''));
		// Ex: $this->loadActions(array(''));
		/* page dependencies */
	}
	
	protected function handleRequest(){
		/* business logic */
	}
	
	public function renderPage(){
		require(dirname(__FILE__).'/../Views/pages/SitemapXML/layout.php');
		exit;
	}
}