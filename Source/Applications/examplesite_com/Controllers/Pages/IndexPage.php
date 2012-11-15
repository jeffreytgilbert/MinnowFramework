<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class IndexPage extends PageController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
// 		$this->loadModels(array());
// 		$this->loadActions(array());
	}
	
	protected function handleRequest(){
		// business logic here
// 		pr($this->getHelpers()->HybridAuth()->getConnectedStates());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedProfiles());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedContacts());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedActivity());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedTimelines());
// 		pr($this->getHelpers()->HybridAuth()->setConnectedStatuses('http://www.youtube.com/watch?v=ENXvZ9YRjbo'));
//		pr($this->getHelpers()->BrowserDetection()->isStandardBrowser());
//		$loc = $this->getHelpers()->Location();
//		pr($loc->getLocationFromYahoo($loc->guessIP()));
//		pr($loc->getLocationFromServices('74.125.225.96'));
	}
	
	public function renderJSON(){ parent::renderJSON(); }
	public function renderXML(){ parent::renderXML(); }
	public function renderHTML(){ parent::renderHTML(); }
	
}
