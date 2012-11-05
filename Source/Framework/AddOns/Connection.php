<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

abstract class Connection {
	
	// set up read only variables
	protected $_Config;
	public function getConfig(){ return $this->_Config; } // this should probably be a model object for simplicity in calling unknown values without running checks
	
	protected $_connection_name;
	public function getConnectionName(){ return $this->_connection_name; }
	
	protected $_instance;
	abstract public function getInstance(); // leave this to be defined by connection driver so it can be type cast correctly
	
	// create default constructor
	public function __construct(Model $Config, $connection_name='default'){
		$this->_Config = $Config;
		$this->_connection_name = $connection_name;
		
		$this->_instance = $this; // by default, assume this connection references it's own class, but allow for driver makers to assign their own classes here.
	}
	
	// require all connections to have destruct methods
	abstract public function __destruct();
	
}
