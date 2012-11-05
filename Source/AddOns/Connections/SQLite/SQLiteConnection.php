<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

// @todo rip out all of the line/file location stuff and put in proper error logging with stack trace 

final class SQLiteConnection extends Connection{

	public function __construct(Model $Config, $connection_name='default'){
		// run parent construct action to save settings
		parent::__construct($Config, $connection_name);
		
		Run::fromConnections('SQLite/Requirements/SQLiteAbstraction.php');
		Run::fromConnections('SQLite/Requirements/SQLiteRequest.php');
				
		$this->_instance = $SQLiteAbstraction = new SQLiteAbstraction(
			$this->getConfig()->getString('db'),
			true,
			$this->getConfig()->getBoolean('debug')
		);
		
		return $SQLiteAbstraction;
	}

	public function getInstance(){
		if($this->_instance instanceof SQLiteAbstraction) return $this->_instance;
		return new SQLiteAbstraction(); // this should never happen. 
	}

	public function __destruct(){
		// unset($this);
	}
}