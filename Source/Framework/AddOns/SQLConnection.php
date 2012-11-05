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
}
