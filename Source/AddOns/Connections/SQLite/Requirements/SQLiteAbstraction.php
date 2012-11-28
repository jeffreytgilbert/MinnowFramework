<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

class SQLiteAbstraction extends SQLConnection{
	
	private $_db, $_autocommit, $_debug=true;
	
	/**
	 * Open a connection to your database
	 * @param string $host
	 * @param string $user
	 * @param string $pass
	 * @param string $db
	 * @param bool $autocommit
	 */
	public function __construct($db='', $autocommit=true, $debug=true){
		$this->_db = $db;
		$this->_autocommit = $autocommit;
		$this->_debug = $debug;
		$this->open();
	}
	
	public function __destruct(){
//		if($this->host==SESSION_HOST) { session_write_close(); }
		self::close();
	}

	/**
	 * Opens a new connection to your database
	 * @return resource
	 */
	public function open(){
		try {
			$this->db_handle = new PDO('sqlite:'.$this->_db);
			$this->db_handle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (PDOException $e){
			if($this->_debug){
				pr($e);
			} else {
				die('Unable to connect using mysql settings for connection: '.$this->_connection_name);
			}
		}
		
		return $this->db_handle;
	}
	
	/**
	 * Close connection to your database
	 * @return bool
	 */
	public function close(){
		return true;
	}

	/**
	 * Run a query and store the results in an array
	 * @param string $query
	 * @return bool
	 */
	public function query($query){
		$query=trim($query);
		if(empty($query)) { return false; }
		
		// Grab the first word from a query which is assumed to be the query type (ie select insert delete update etc...)
		$explosion=explode(' ', strtolower(trim(str_replace('(','',$query))));
		$this->last_type=array_shift($explosion);
		
		$this->all_data=array();
		$this->row_data=array();
		try {
			$time_start = microtime(true);
			$result=$this->db_handle->query($query);
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$this->queries[]=$query;
			if(round($time,4) > 3) { $this->slow_queries[]=$query; }
			$this->statement=$result;
			$this->rows=$this->statement->rowCount();
			$this->next_row_number=0;
		} catch (PDOException $e) {
			if($this->_debug){ pr($e); }
			$time_end = microtime(true);
			$time = $time_end - $time_start;
			$this->bad_queries[]=$query.'; #'.$this->db_handle->errorCode();
			return false;
		}
		
		return true;
	}

	/**
	 * Prepare an sql statement for execution
	 * @param string $query
	 */
	public function prepare($query){
		// Grab the first word from a query which is assumed to be the query type (ie select insert delete update etc...)
		$explosion=explode(' ', strtolower(trim(str_replace('(','',$query))));
		$this->last_type=array_shift($explosion);
		$time_start = microtime(true);
		try {
			$this->statement=$this->db_handle->prepare($query);
		} catch (PDOException $e) {
			if($this->_debug){ pr($e); }
			$this->bad_queries[]=$query.'; #'.$e->getMessage();
			return false;
		}
		
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$this->queries[]=$query;
		
		return true;
	}
	
	/**
	 * Execute a previously prepared sql statement
	 * @param array $values
	 */
	public function execute(Array $values, Array $integers=array()){ // the integers part is a compatiblity fix for this bug: http://bugs.php.net/bug.php?id=40740
		// loop must iterate this way because bind creates a pointer to the memory and a foreach that creates key and value vars reuses the same memory space for the value
		$keys = array_keys($values);
		try {
			foreach($keys as $key){
				if(lower($values[$key]) === 'null' || !isset($values[$key])){
	//				echo 'Null: '.$key.'='.$values[$key]."\n<br>";
					$this->statement->bindParam($key, $values[$key], PDO::PARAM_NULL);
				} else if(in_array($key, $integers)){
	//				echo 'Int: '.$key.'='.$values[$key]."\n<br>";
					$values[$key] = intval($values[$key]);
					$this->statement->bindParam($key, $values[$key], PDO::PARAM_INT);
				} else {
	//				echo 'String: '.$key.'='.$values[$key]."\n<br>";
					$this->statement->bindParam($key, $values[$key]);
				}
			}
		} catch (PDOException $e) {
			if($this->_debug){ pr($e); }
			$this->bad_queries[]=current($this->queries).'; #'.$e->getMessage();
//			echo $query;
			return false;
		}
		
		$time_start = microtime(true);
		try {
			// run it
			$this->statement->execute(); // $values
		} catch (PDOException $e) {
			if($this->_debug){ pr($e); }
			$this->bad_queries[]=current($this->queries).'; #'.$e->getMessage();
//			echo $query;
			return false;
		}
		// clock it
		$time_end = microtime(true);
		$time = $time_end - $time_start;
		if(round($time,4) > 3) { $this->slow_queries[]=current($this->queries); }
		
		$this->all_data=array();
		$this->row_data=array();
		$this->next_row_number=0;
		$this->affected_rows=$this->statement->rowCount();
		return true;
	}
	
	/**
	 * Terminate the last prepared sql statement
	 * @param array $values
	 */
	public function terminate() { /*mysqli_stmt_close($this->statement);*/ }
	
	/**
	 * Read one row of data from the results of the last query
	 * @return bool
	 */
	public function readRow($return_type='ASSOC'){
		if(count($this->all_data) == 0){
			switch($return_type)
			{
				case 'BOTH' : $this->all_data=$this->statement->fetchAll(PDO::FETCH_BOTH); break;
				case 'NUM' : $this->all_data=$this->statement->fetchAll(PDO::FETCH_NUM); break;
				default : $this->all_data=$this->statement->fetchAll(PDO::FETCH_ASSOC); break;
			}
		}
		if(count($this->all_data) == 0){ return false; }

		$this->row_data = array();
		$result = each($this->all_data);

		if(isset($result['key']) && isset($result['value'])){
			$this->row_data = $result['value'];
			$this->next_row_number = $this->next_row_number+1;
			return true;
		}
		return false;
	}

	public function read($return_type='ASSOC'){
		//		echo 'fetch all';
		switch($return_type)
		{
			case 'BOTH' : $this->all_data=$this->statement->fetchAll(PDO::FETCH_BOTH); break;
			case 'NUM' : $this->all_data=$this->statement->fetchAll(PDO::FETCH_NUM); break;
			default : $this->all_data=$this->statement->fetchAll(PDO::FETCH_ASSOC); break;
		}
		$this->next_row_number = 0;
		return $this->all_data;
	}
	
	/**
	 * Commit queries on current transaction
	 * @return bool
	 */
	public function commit() { return $this->db_handle->commit($this->db_handle); }

	/**
	 * Undo the queries in the transaction
	 * @return bool
	 */
	public function rollback() { return $this->db_handle->rollback($this->db_handle); }
	
	/**
	 * Escape strings to prepare them for insert.
	 */
	final public function escape($data) { return $this->db_handle->quote($data); }

	final public static function string($str, $max=null)
	{
		if(empty($str)) { return ''; }
		$str=strval($str);
	
		if(isset($max)) { $str = substr($str,0,$max); }
	
		return RuntimeInfo::instance()->getConnections()->SQLite()->escape($str);
	}
	
	/**
	 * Filter an array of strings into a WHERE blah IN ready format
	 * @param array $array
	 * @return string
	 */
	final public static function strings($array)
	{
		$array=array_unique($array);
		sort($array);
		$array=array_filter($array,'SQLiteAbstraction::string');
		$string=implode('","',$array);
		return '("'.$string.'")';
	}
	
	/**
	 * Return the number of affected rows
	 */
	final public function affectedRows() { return $this->statement->rowCount(); }
	
	/**
	 * Return the last inserted id
	 */
	final public function insertId() { return $this->db_handle->lastInsertId(); }
	
}