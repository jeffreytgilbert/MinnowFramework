<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class WelcomeComponentController extends ComponentController{
	protected function loadIncludedFiles(){
	}
	
	public function getParentComponent(){ return AuthenticationComponent::cast($this->_ParentObject); }
	
	protected $_Authentication;
	
	public function handleRequest(){
		
		$this->_Authentication = $this->getParentComponent();
		
		$ID = $this->_Authentication->identifyUser();
		
		// check to see if the person is logged in or identified
		if(!$ID->isOnline()){ $this->redirect('/'); }
	
		$this->setInput('SubRegistration', array(
			'first_name'=>$ID->getUserAccount()->getString('first_name'),
			'last_name'=>$ID->getUserAccount()->getString('last_name')
		));
		
		$HybridAuth = $this->_Authentication->getHybridAuth();
		
		$connected_profiles = $HybridAuth->getConnectedProfiles();
		foreach($connected_profiles as $ConnectedProfile){
			if(!is_null($ConnectedProfile)){
				$ConnectedProfile = $HybridAuth->castAsHAProfile($ConnectedProfile);
				if(!empty($ConnectedProfile->emailVerified)){
					$this->getInput('SubRegistration')->set('unique_identifier',$ConnectedProfile->emailVerified);
					break;
				}
				if(!empty($ConnectedProfile->email)){
					$this->getInput('SubRegistration')->set('unique_identifier',$ConnectedProfile->email);
					break;
				}
			}
		}
	}
	
// 	public function renderJSON(){ return parent::renderJSON(); }
// 	public function renderXML(){ return parent::renderXML(); }
	public function renderHTML(){
		
		$PageController = PageController::cast($this->getParentComponent()->getParentController());
		$PageController->addCss('Libraries/Zocial/zocial');
		$PageController->addCss('Components/Authentication/Pages/Welcome');
//		$PageController->addJs('Libraries/jQuery.Validate/jquery.validate');
		$PageController->addJs('Components/Authentication/Pages/Welcome');
		
		return parent::renderHTML();
	}
	
}
