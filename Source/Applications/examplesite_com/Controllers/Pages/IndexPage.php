<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class IndexPage extends PageController implements HTMLCapable, JSONCapable, XMLCapable{
	protected function loadIncludedFiles(){
 		$this->loadModels(array('Notification','User'));
 		$this->loadActions(array('AvatarImageActions'));
	}
	
	protected function handleRequest(){
		// business logic here
// 		pr($this->getHelpers()->HybridAuth()->getConnectedStates());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedProfiles());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedContacts());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedActivity());
// 		pr($this->getHelpers()->HybridAuth()->getConnectedTimelines());
// 		pr($this->getHelpers()->HybridAuth()->setConnectedStatuses('http://www.youtube.com/watch?v=ENXvZ9YRjbo'));
//		pr($this->getHelpers()->BrowserDetection()->isStandardBrowser());
//		$loc = $this->getHelpers()->Location();
//		pr($loc->getLocationFromYahoo($loc->guessIP()));
//		pr($loc->getLocationFromServices('74.125.225.96'));
//		pr($_SESSION);
// 		$NotificationCollection = new NotificationCollection();
// 		$NotificationCollection->addObject(new Notification(array('user_id'=>1,'created_datetime'=>'now')));
// 		$NotificationCollection->addObject(new Notification(array('message'=>'hi','primary_link'=>'/')));
// 		$NotificationCollection->addObject(new Notification(array('User'=>new User(array('user_id'=>3,'password'=>'my neck, my back, my...')))));
		
//		pr($NotificationCollection->toArrayRecursive());
//		pr($NotificationCollection->toArrayRecursive(10,array('primary_link','User'=>array('password'))));

// 		$UserCollection = new UserCollection(array(
// 			new User(array('login_name'=>'Ryan Jones')),
// 			new User(array('login_name'=>'Jesus Jones')),
// 			new User(array('login_name'=>'Jeff Gilbert')),
// 			new User(array('login_name'=>'Jeffrey Gilbert')),
// 			new User(array('login_name'=>'Topless Tapas'))
// 		));
		
// 		$results = $UserCollection->searchObjectByField('login_name', 'Gilbert');
		
// 		pr(count($results));
// 		pr($results);

//		pr($this->getHelpers()->SecureHash()->generateSecureHash('My voice is my passport'));
// 		$this->getHelpers()->SecureHash();
// 		$this->getConnections()->AmazonS3();
// 		$this->getConnections()->MySQL();
// 		$this->getConnections()->SQLite();
// 		$this->getConnections()->Postmark();
// 		$this->getConnections()->Memcached();
		
// 		AvatarImageActions::createAvatar(array(
// 			'http://us3.php.net/images/php.gif',
// 			'https://s-static.ak.fbcdn.net/rsrc.php/v2/ye/x/WlrEvsTFI5C.png',
// 		));
	}
	
	public function renderJSON(){ parent::renderJSON(); }
	public function renderXML(){ parent::renderXML(); }
	public function renderHTML(){ parent::renderHTML(); }
	
}
