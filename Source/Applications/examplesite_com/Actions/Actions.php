<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// This actions class is meant to serve as a Helper for Models / Controllers (like a command is for CakePHP, but more tailored to a data type in most cases)
// This is legacy functionality, and will be removed in lieu of Trait based Connector methods which do the same things but in the Connector definition, not in some actions class

Run::fromConnections('AmazonS3/AmazonS3Actions.php');
Run::fromConnections('MySQL/MySQLActions.php');
Run::fromConnections('SQLite/SQLiteActions.php');
Run::fromConnections('Memcached/MemcachedActions.php');

abstract class Actions{
	
	// add sugar for converting data from connection source to native objects and collections
	use AmazonS3Actions, MySQLActions, SQLiteActions, MemcachedActions;
	
	public static function createRandomCode() { 
	    $chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
	    srand((double)microtime()*1000000); 
	    $i = 0; 
	    $pass = '' ; 
	
	    while ($i < 16) {
	        $num = rand() % 33;
	        $tmp = substr($chars, $num, 1);
	        $pass = $pass . $tmp;
	        if( ($i%4) == 3 && ($i+1) < 16 ){ $pass .= '-'; } 
	        $i++;
	    }
	
	    return upper($pass);
	}
	
}