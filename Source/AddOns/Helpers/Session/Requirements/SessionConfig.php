<?php

class SessionConfig{
	private
		$_hosts,
		$_storage_method,
		$_timeout;

	const
		_USE_MEMCACHED = 'use_memcached',
		_USE_MYSQL = 'use_mysql',
		_USE_FILE_SYSTEM = 'use_file_system';
	
	public function __construct($storage_method=self::_USE_FILE_SYSTEM, $hosts, $timeout=1440){
		if(!in($storage_method,array(
				self::_USE_MEMCACHED,
				self::_USE_MYSQL,
				self::_USE_FILE_SYSTEM
		))){
			die('Call to create session is using unsupported storage method. Check SessionConfig for details.');
		}
		$this->_hosts = $hosts;
		$this->_storage_method = $storage_method;
		$this->_timeout;
	}
	
	public function getHosts(){
		return $this->_hosts;
	}

	public function getTimeout(){
		return $this->_timeout;
	}

	public function isUsingMemcached(){ return ($this->_storage_method == self::_USE_MEMCACHED); }
	public function isUsingMySQL(){ return ($this->_storage_method == self::_USE_MYSQL); }
	public function isUsingFileSystem(){ return ($this->_storage_method == self::_USE_FILE_SYSTEM); }
}
