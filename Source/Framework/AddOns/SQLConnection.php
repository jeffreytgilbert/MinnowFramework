<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */


/**
 * I got this abstraction layer from zend.com's code gallery under abstraction classes version 1.2
 * This I guess would be 1.3 since it's running under php5 strict, not php4.
 * I changed the casing, white spacing, and names to conform to common practice
 * @package CoreComponents
 */
abstract class SQLConnection
{
	protected $host				= '';
	protected $db_handle		= null;
	protected $statement		= null;
	protected $query_result		= null;
	protected $autocommit		= true;
	protected $next_row_number	= 0;
	protected $affected_rows	= 0;
	protected $last_type		= '';
	protected $prepared_format	= array();
	protected $bad_queries		= array();
	protected $slow_queries		= array();
	protected $queries			= array();
	
	public $all_data			= array();
	public $row_data			= array();
	
	const LIKE_WC_LAST = 'last';
	const LIKE_WC_FIRST = 'first';
	const LIKE_WC_FIRST_LAST = 'first_last';
	const LIKE_WC_SPACES = 'spaces';
		
	/**
	 * Disconnects from the database
	 */
	abstract function __destruct();

	/**
	 * open connection to current db
	 */
	abstract function open();
	
	/**
	 * Close connection to current db
	 */
	abstract function close();
	
	/**
	 * Queries the database
	 * @param string $query
	 */
	abstract function query($query);
	
	/**
	 * Prepare an sql statement for execution
	 * @param string $query
	 */
	abstract function prepare($query);
	
	/**
	 * Execute a previously prepared sql statement
	 * @param array $values
	 */
	abstract function execute(Array $values, Array $integers=array()); // the integers part is a compatiblity fix for this bug: http://bugs.php.net/bug.php?id=40740
	
	/**
	 * Terminate the last prepared sql statement
	 * @param array $values
	 */
	abstract function terminate();
	
	/**
	 * Reads one row of data
	 */
	abstract function readRow($return_type='ASSOC');
	
	/**
	 * Commit queries in  current transaction
	 */
	abstract function commit();
	
	/**
	 * Rollback queries in current transaction
	 */
	abstract function rollback();
	
	/**
	 * Escape strings for queries (useful for things like sessions.)
	 */
	abstract function escape($data);
	
	public function like($string, $style){
		$string = $this->db_handle->quote($string);
		switch($style){
			case SQLConnection::LIKE_WC_FIRST:
				return "'%".substr($string,1);
			case SQLConnection::LIKE_WC_LAST:
				return substr($string,0,-1)."%'";
			case SQLConnection::LIKE_WC_FIRST_LAST:
				return "'%".substr($string,1,-1)."%'";
			case SQLConnection::LIKE_WC_SPACES:
				return preg_replace('/\s+/', '%', "'%".substr($string,1,-1)."%'");
			default:
				die('Unsupported format. Use SQLConnection constants.');
		}
	}
	
	/**
	 * Return the number of affected rows
	 */
	abstract function affectedRows();
	
	/**
	 * Return the last inserted id
	 */
	abstract function insertId();
	
	/**
	 * Turn autocommits on or off
	 * @param bool $autocommit
	 */
	final public function setAutoCommit($autocommit) { $this->autocommit=$autocommit; }
	
	/**
	 * Returns all queries that errored and the reason of the error in an array.
	 * @return array
	 */
	final public function getErrors() { return $this->bad_queries; }
	
	/**
	 * Returns all queries that ran
	 * @return array
	 */
	final public function getQueries() { return $this->queries; }
	
	/**
	 * Returns slow queries that ran
	 * @return array
	 */
	final public function getSlowQueries() { return $this->slow_queries; }
	
	/**
	 * Force the string entered into a var of type bool
	 * @param int $str
	 * @return int
	 */
	public static function boolean($str)
	{
		if(!isset($str) || empty($str)) { return 'NULL'; }
		return '1';
	}
	
	/**
	 * Force the string entered into a var of type int within the span declared
	 * also can return NULL if $str is not set
	 * @param int $str
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function integer($str, $min=null, $max=null)
	{
		$int = (int)$str;
		if(isset($min) && $int < $min)		{ return (int)$min; }
		else if(isset($max) && $int > $max)	{ return (int)$max; }
		return $int;
	}
	
	/**
	 * Force the string entered into a var of type int within the span declared
	 * Like Filter::integer, but always returns type int, returns default if set and $str is not
	 * @param int $str
	 * @param int $min
	 * @param int $max
	 * @return int
	 */
	public static function range($str, $min=-2147483648, $max=2147483647, $default=null) //32 bit values
	{
	
		if(isset($default) && !isset($str))	{ $int= (int)$default; }
		else 									{ $int= (int)$str; }
	
		$min= (int)$min;
		$max= (int)$max;
		if($int < $min)		{ return $min; }
		elseif($int > $max)	{ return $max; }
		return $int;
	}
	
	/**
	 * Filters the raw text path to an image or file for articles and such.
	 * @param string $str
	 * @return string
	 */
	public static function filename($str)
	{
		if(!isset($str)) { return ''; }
		$str = preg_replace('/[?*<>|]/','', $str);
		return $str;
	}
	
	/**
	 * Filter text into only letters, numbers, and underscores to prevent sql injection attacks
	 * @param array $string
	 * @return string
	 */
	public static function column($str)
	{
		if(!isset($str)) { return ''; }
		return preg_replace('/([^a-zA-Z0-9_\-])/s','',$str);
	}
	
	public static function string($str, $max=null)
	{
		if(empty($str)) { return ''; }
		$str=strval($str);
	
		if(isset($max)) { $str = substr($str,0,$max); }
	
		return self::escape($str);
	}
	
	/**
	 * Filter an array of strings into a WHERE blah IN ready format
	 * @param array $array
	 * @return string
	 */
	public static function strings($array)
	{
		$array=array_unique($array);
		sort($array);
		$array=array_filter($array,'SQLConnection::string');
		$string=implode('","',$array);
		return '("'.$string.'")';
	}
	
	/**
	 * Filter an array of ints into a WHERE blah IN ready format
	 * @param array $array
	 * @return string
	 */
	public static function integers($array)
	{
		if(!is_array($array)) { $array=array($array); }
	
		$array=array_unique($array);
		sort($array);
		$array=array_filter($array,'intval');
		$string=implode('","',$array);
		return '("'.$string.'")';
	}
	
}
