<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class LoginComponentController extends ComponentController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
	}
	
	public function handleRequest(){
		$this->getData()->set('thisiswhatitsoundslike','whenthedovescry');
	}
	
	public function renderJSON(){ return parent::renderJSON(); }
	public function renderXML(){ return parent::renderXML(); }
	public function renderHTML(){ return parent::renderHTML(); }
	
}
