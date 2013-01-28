<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class HybridAuthEndpointComponentController extends ComponentController{
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	public function handleRequest(){
		
// 		if ( strrpos( $_SERVER["QUERY_STRING"], '?' ) ) {
// 			$_SERVER["QUERY_STRING"] = str_replace( "?", "&", $_SERVER["QUERY_STRING"] );

// 			parse_str( $_SERVER["QUERY_STRING"], $_REQUEST );
// 			if(isset( Hybrid_Endpoint::$request["hauth_done"] ) && Hybrid_Endpoint::$request["hauth_done"]){
// 				header("Content-Type: text/plain");
// 				echo '<html>';
// 				pr('This is creating redirect loop errors');
// 				ob_flush();
// 				ob_flush();
// 				ob_flush();
// 				ob_flush();
// 				ob_flush();
// 			}
// 		}
		
		$HybridAuth = $this->getHelpers()->HybridAuth();
		if(count($HybridAuth->getErrors()) > 0){
			pr($HybridAuth->getErrors());
			$errors = $HybridAuth->getErrors();
			$e = array_pop($errors);
// 			die;
			if($e instanceof HybridAuthException){
				$this->flashError($e->getCode(), $e->getMessage());
				$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
// 				$this->redirect('/');
			}
		} else {
			$HybridAuth->endpoint();
			if(count($HybridAuth->getErrors()) > 0){
				pr($HybridAuth->getErrors());
				$errors = $HybridAuth->getErrors();
				$e = array_pop($errors);
// 				die;
				if($e instanceof HybridAuthException){
					$this->flashError($e->getCode(), $e->getMessage());
					$this->redirect($this->getParentComponent()->getConfig()->get('login_page_url'));
// 					$this->redirect('/');
				}
			} else {
				$this->redirect('/');
			}
		}
		
	}

	
}