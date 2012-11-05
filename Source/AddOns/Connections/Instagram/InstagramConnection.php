<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

final class InstagramConnection extends Connection{
	
	public function __construct(Model $Config, $connection_name='default'){
		// run parent construct action to save settings
		parent::__construct($Config, $connection_name);
		
		Run::fromConnections('Instagram/Requirements/Instagram.php');
		
		$this->_instance = $Instagram = new Instagram(array(
			'apiKey'=>$this->getConfig()->getString('api_key'),
			'apiSecret'=>$this->getConfig()->getString('api_secret'),
			'apiCallback'=>$this->getConfig()->getString('callback_url')
		));
		
		return $Instagram;
	}
	
	public function getInstance(){
		return ($this->_instance instanceof Instagram)?$this->_instance:new Instagram();
	}
	
	public function __destruct(){
		// unset($this);
	}
}

