<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class EndPointPage extends PageController implements HTMLCapable{
	protected function loadIncludedFiles(){
	}
	
	protected function handleRequest(){
		$this->getHelpers()->HybridAuth()->endpoint();
	}

	public function renderHTML(){ parent::renderHTML(); }
	
}