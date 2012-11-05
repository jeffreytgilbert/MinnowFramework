<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class Err404Page extends TemplatedPageRequest{
	protected function loadIncludedFiles(){
	}
	
	protected function handleRequest(){
	}
	
	public function renderPage(){
		$this->_page_body = $this->runCodeReturnOutput('pages/Err404/layout.php');
	}
}