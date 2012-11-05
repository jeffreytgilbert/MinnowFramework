<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

// @todo rip out all of the line/file location stuff and put in proper error logging with stack trace 

final class MySQLConnection extends Connection{

	public function __construct(Model $Config, $connection_name='default'){
		// run parent construct action to save settings
		parent::__construct($Config, $connection_name);
		
		Run::fromConnections('MySQL/Requirements/MySQLAbstraction.php');
		Run::fromConnections('MySQL/Requirements/MySQLRequest.php');
		
		$this->_instance = $MySQLAbstraction = new MySQLAbstraction(
			$this->getConfig()->getString('host'),
			$this->getConfig()->getString('username'),
			$this->getConfig()->getString('password'),
			$this->getConfig()->getString('schema'),
			$this->getConfig()->getString('port'),
			true,
			$this->getConfig()->getBoolean('debug')
		);
		
		$sql = 'SET time_zone = :time_zone';
		$MySQLAbstraction->prepare($sql,__LINE__,__FILE__);
		$MySQLAbstraction->execute(array(':time_zone'=>RuntimeInfo::instance()->config(null,null,'base_time'))); 
		
// 		$sql = 'SELECT NOW() AS right_now_gmt';
// 		$MySQLAbstraction->query($sql,__LINE__,__FILE__);
// 		$MySQLAbstraction->readRow();
		//define('RIGHT_NOW_GMT', $connection->row_data['right_now_gmt']);
		
		return $MySQLAbstraction;
	}

	public function getInstance(){
		if($this->_instance instanceof MySQLAbstraction) return $this->_instance;
		return new MySQLAbstraction(); // this should never happen. 
	}

	public function __destruct(){
		// unset($this);
	}
}
