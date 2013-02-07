<?php

class SitemapXMLPage extends PageController implements HTMLCapable{
	
	public function handleRequest(){
		
		// Requery the database for the ones that exist
		$SitemapCollection = SitemapActions::selectList();
		
		// Put them in a data field so they can be used on the view
		$this->getDataObject()->set('SitemapCollection',$SitemapCollection);
	}
	
	public function renderHTML(){
		
		require(Path::toViews().'/Pages/SitemapXML/layout.php');
		exit;
	}
}