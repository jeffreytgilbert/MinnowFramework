<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class HybridAuthEndpointComponentController extends ComponentController{
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		$this->getHelpers()->HybridAuth()->endpoint();
		$errors = $this->getHelpers()->HybridAuth()->getErrors();
		$e = array_pop($errors);
		if($e instanceof HybridAuthException){
			$this->flashError($e->getCode(), $e->getMessage());
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
		} else {
			$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
		}
	}

	public function renderHTML(){ parent::renderHTML(); }
	
}