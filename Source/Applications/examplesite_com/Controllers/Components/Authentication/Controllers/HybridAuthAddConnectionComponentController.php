<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class HybridAuthAddConnectionComponentController extends ComponentController { // this ends up somewhere, but never here
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected $_Authentication;
	
	public function handleRequest(){
		
		$this->_Authentication = $this->getParentComponent();
		
		$ID = $this->_Authentication->identifyUser();
		
		// check to see if the person is logged in or identified
		if($ID->isOnline()){
			
			// This page requires a provider to be provided to function as a social sign on page. Check for that provider
			if(_g('provider')){
				$HybridAuth = $this->getHelpers()->HybridAuth();
				$this->_data['providers'] = array_keys($HybridAuth->getAvailableProviders());
				
				// Is that provider in the provider list that corresponds to the providers configured in hybrid auth settings
				if(in(_g('provider'), $this->_data['providers'])){
					if(count($HybridAuth->getErrors()) == 0){
						// do the actual social sign on portion of this authentication
						$this->_page_body = $HybridAuth->authenticate(lower(_g('provider')));
						
						// Try to authenticate the user and get the result back
						switch($this->_Authentication->authenticateNewHybridAuthConnection($ID)){
							case AuthenticationComponent::ADD_AUTHENTICATION_SUCCESS:
								$this->flashConfirmation('LoginSuccessful', 'The sign on was linked to your account.');
								break;
							case AuthenticationComponent::ADD_AUTHENTICATION_ERROR_NO_CONNECTED_PROVIDERS:
								$this->flashError('LoginFailed', 'No providers have been successfully connected.');
								break;
							case AuthenticationComponent::ADD_AUTHENTICATION_ERROR_DUPLICATE_ENTRY:
								$this->flashError('LoginFailed', 'Another account is already linked up with this provider account. Could not link provider.');
								break;
							default:
								$this->flashError('LoginFailed', 'Unknown Error.');
								break;
						}
						
						$this->redirect($this->getParentComponent()->getConfig()->get('welcome_page_url'));
					} else {
						$errors = $HybridAuth->getErrors();
						$e = array_pop($errors);
// 						pr('Login attempt failed');
						if($e instanceof HybridAuthException){
							$this->flashError($e->getCode(), $e->getMessage());
							$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
						}
					}
				// if not, send back to the login page
				} else {
// 					pr('Invalid provider');
					$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
				}
			// if no provider is set, send back to the login page to get one
			} else {
// 				pr('No provider set');
				$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
			}
		} else {
// 			pr('Not logged in');
			$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
		}
	}

}