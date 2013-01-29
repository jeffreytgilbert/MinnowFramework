<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class MemcachedConnection extends Connection{

	public function __construct(Model $Config, $connection_name='default'){
		// run parent construct action to save settings
		parent::__construct($Config, $connection_name);
		
		Run::fromConnections('Memcached/Requirements/classes/MemcachedAbstraction.php');
		
		$this->_instance = $MemcachedAbstraction = MemcachedAbstraction::instance($connection_name);
		
		$failure_callback = array();
		
		$this->_instance->connect(array(
			'default' => array(
				'host' => $this->getConfig()->getString('host'),
				'port' => $this->getConfig()->getString('port'),
				'prefix' => $this->getConfig()->getString('prefix'),
				'weight' => $this->getConfig()->getString('weight'),
				'persistent' => $this->getConfig()->getBoolean('persistent'),
			)
		));
		
//		pr($this->_instance);
		
		return $MemcachedAbstraction;
	}

	public function getInstance(){
		if($this->_instance instanceof MemcachedAbstraction) return $this->_instance;
		return new MemcachedAbstraction(); // this should never happen. 
	}

	public function __destruct(){
		// unset($this);
	}
}
