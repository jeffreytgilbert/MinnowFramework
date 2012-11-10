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
	}
	
	public function renderJSON(){ parent::renderJSON(); }
	public function renderXML(){ parent::renderXML(); }
	public function renderHTML(){ parent::renderHTML(); }
	
}
