<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class Connections{
	
	public static function cast(Connections $Connections){ return $Connections; }
	
	use ConfigReader;
	private $_connections = array();
	
	// make sure connections are freed at the end of a process execution
	public function __destruct(){
		foreach($this->_connections as $ConnectionType){
			foreach ($ConnectionType as $Connection){
				if($Connection instanceof Connection){ $Connection->__destruct(); }
			}
		}
	}
	
	// example usage: 
	// RuntimeInfo->Connections->MySQL('default')->prepare($query, $line, $file);
	
	// 	this is how connector/drivers should be installed. This code can be reused identically while just changing the instance name of each connection type
	
	public function AmazonS3($connection_name='default'){
		if(isset($this->_connections['AmazonS3'][$connection_name])
				&& $this->_connections['AmazonS3'][$connection_name] instanceof AmazonS3Connection){
			return $this->_connections['AmazonS3'][$connection_name]->getInstance();
		}
		Run::fromConnections('AmazonS3/AmazonS3Connection.php');
		$this->_connections['AmazonS3'][$connection_name] = $AmazonS3Connection = new AmazonS3Connection($this->config('Connections/AmazonS3/', $connection_name));
		return $AmazonS3Connection->getInstance();
	}
	
	public function Instagram($connection_name='default'){
		if(isset($this->_connections['Instagram'][$connection_name])
				&& $this->_connections['Instagram'][$connection_name] instanceof InstagramConnection){
			return $this->_connections['Instagram'][$connection_name]->getInstance();
		}
		Run::fromConnections('Instagram/InstagramConnection.php');
		$this->_connections['Instagram'][$connection_name] = $InstagramConnection = new InstagramConnection($this->config('Connections/Instagram/', $connection_name));
		return $InstagramConnection->getInstance();
	}
	
	public function Memcached($connection_name='default'){
		if(isset($this->_connections['Memcached'][$connection_name])
				&& $this->_connections['Memcached'][$connection_name] instanceof MemcachedConnection){
			return $this->_connections['Memcached'][$connection_name]->getInstance();
		}
		Run::fromConnections('Memcached/MemcachedConnection.php');
		$this->_connections['Memcached'][$connection_name] = $MemcachedConnection = new MemcachedConnection($this->config('Connections/Memcached/', $connection_name));
		return $MemcachedConnection->getInstance();
	}
	
	public function MySQL($connection_name='default'){
		if(isset($this->_connections['MySQL'][$connection_name])
				&& $this->_connections['MySQL'][$connection_name] instanceof MySQLConnection){
			return $this->_connections['MySQL'][$connection_name]->getInstance();
		}
		Run::fromConnections('MySQL/MySQLConnection.php');
		$this->_connections['MySQL'][$connection_name] = $MySQLConnection = new MySQLConnection($this->config('Connections/MySQL/', $connection_name));
		return $MySQLConnection->getInstance();
	}
	
	public function Postmark($connection_name='default'){
		if(isset($this->_connections['Postmark'][$connection_name])
			&& $this->_connections['Postmark'][$connection_name] instanceof PostmarkConnection){
			return $this->_connections['Postmark'][$connection_name]->getInstance();
		}
		Run::fromConnections('Postmark/PostmarkConnection.php');
		$this->_connections['Postmark'][$connection_name] = $PostmarkConnection = new PostmarkConnection($this->config('Connections/Postmark/', $connection_name));
		return $PostmarkConnection->getInstance();
	}

	public function SQLite($connection_name='default'){
		if(isset($this->_connections['SQLite'][$connection_name])
				&& $this->_connections['SQLite'][$connection_name] instanceof SQLiteConnection){
			return $this->_connections['SQLite'][$connection_name]->getInstance();
		}
		Run::fromConnections('SQLite/SQLiteConnection.php');
		$this->_connections['SQLite'][$connection_name] = $SQLiteConnection = new SQLiteConnection($this->config('Connections/SQLite/', $connection_name));
		return $SQLiteConnection->getInstance();
	}
	
}


