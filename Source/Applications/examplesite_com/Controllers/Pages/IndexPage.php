<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class IndexPage extends PageController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
// 		$this->loadModels(array(
// 		));
// 		$this->loadActions(array(
// 		));
//		echo 'this gets called';
	}
	
	protected function handleRequest(){
//		echo 'this gets called 2';
		
	}
	
	public function renderJSON(){ self::renderJSON(); }
	public function renderXML(){ self::renderXML(); }
	
	public function renderPage(){
//		echo 'this gets called 3';
 		$this->addCss('Pages/Index');
 		$this->addJs('Pages/Index');
		
		$this->_page_body = $this->runCodeReturnOutput('Pages/Index/layout.php');
	}
}
