<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class HybridAuthLoginRequestComponentController extends ComponentController { // this ends up somewhere, but never here
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected $_Authentication;
	
	public function handleRequest(){
		
		$this->_Authentication = $this->getParentComponent();
		
		$ID = $this->_Authentication->identifyUser();
		
		// check to see if the person is logged in or identified
		if($ID->isOnline()){
			// if they're logged in already, send them to the welcome page
			$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url')); // have the welcome page handle further registration steps. This is the social sign in forwarding page.
			
		// This page requires a provider to be provided to function as a social sign on page. Check for that provider
		} else if(_g('provider')){
			$HybridAuth = $this->getHelpers()->HybridAuth();
			$this->_data['providers'] = array_keys($HybridAuth->getAvailableProviders());
			
			// Is that provider in the provider list that corresponds to the providers configured in hybrid auth settings
			if(in(_g('provider'), $this->_data['providers'])){
				if(count($HybridAuth->getErrors()) == 0){
					$this->_Authentication->logout();
					$this->_page_body = $HybridAuth->authenticate(lower(_g('provider')));
					$ID = $this->_Authentication->identifyUser();
					$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
				} else {
					$errors = $HybridAuth->getErrors();
					$e = array_pop($errors);
					if($e instanceof HybridAuthException){
						$this->flashError($e->getCode(), $e->getMessage());
						$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
					}
				}
			// if not, send back to the login page
			} else {
				$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
			}
		// if no provider is set, send back to the login page to get one
		} else {
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
		}
		
	}

}