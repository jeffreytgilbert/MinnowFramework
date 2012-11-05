<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// What if each of the plugins loaded is a trait that can be used, then things could just access them through the action class dependant on the trait needed.
// i like this idea, but each trait needs to have some way to be identified (installed as it were) or would it, since all I'd need to do is autoload them and wait for their use in actions
// but then the connectors could all be required to have config readers and common methods.

// Each connector should be allowed to have a trait or an interface it can inherit from where certain methods and properties are defined that help identify it and allow it to read its own config

// @todo make outputs on DataObject/Models trait savvy so they can output to common html / parser / filter functions

class Connections{
	
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


