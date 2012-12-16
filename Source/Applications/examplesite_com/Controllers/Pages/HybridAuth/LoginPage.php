<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class LoginPage extends PageController implements HTMLCapable{
	protected function loadIncludedFiles(){
	}
	
	public function handleRequest(){
		
		$ID = $this->_Authentication->authenticateFromHybridAuth();
		
//		unset($_SESSION['MINNNOW::ID']);
//		die;
//		pr($ID);
		
		if($ID->isOnline()){ // check to see if the person is logged in, and if so redirect them to the post login page
// 			echo '<h1>Welcome!</h1>';
// 			pr($ID);
			//$ID->getUserAccount()->
			//$this->redirect('/My/Welcome');
			if($ID->getUserAccount()->getString('password_hash') == ''){
				$this->redirect('/Account/Registration');
			} else {
				$this->redirect('/My/Homepage');
			}
		} else { // if the person is not logged in, try to log them in.
			$HybridAuth = $this->getHelpers()->HybridAuth();
			$this->_data['providers'] = array_keys($HybridAuth->getAvailableProviders());
			//pr($HybridAuth);
			if(_g('provider') && in(_g('provider'), $this->_data['providers'])){
				$this->_page_body = $HybridAuth->authenticate(lower(_g('provider'), '/HybridAuth/Login/'));
			}
		}
		
	}

	public function renderHTML(){ 
		if(empty($this->_page_body)){ $this->_page_body = $this->runCodeReturnOutput('Pages/HybridAuth/Login/layout.php'); }
//		pr($_SESSION);
		
	}
	
}