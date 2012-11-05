<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class IndexPage extends TemplatedPageRequest{
	protected function loadIncludedFiles(){
// 		$this->loadModels(array(
// 		));
// 		$this->loadActions(array(
// 		));
	}
	
	protected function handleRequest(){
		
	}
	
	public function renderPage(){
// 		$this->addCss('pages/Index');
// 		$this->addJs('pages/Index');
		
		$this->_page_body = $this->runCodeReturnOutput('pages/Index/layout.php');
	}
}
