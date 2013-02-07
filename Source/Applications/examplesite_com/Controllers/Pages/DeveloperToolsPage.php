<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class DeveloperToolsPage extends PageController implements HTMLCapable, JSONCapable{

	public function handleRequest(){
		
		// Requery the database for the ones that exist
		$SitemapCollection = SitemapActions::selectList();
		
		// Put them in a data field so they can be used on the view
		$this->getDataObject()->set('SitemapCollection',$SitemapCollection);
	}
	
	public function renderJSON(){ return $this->output = parent::renderJSON(); }
	public function renderHTML(){ return $this->_page_body = parent::renderHTML(); }

}
