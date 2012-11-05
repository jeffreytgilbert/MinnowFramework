<?php

class SessionMySQLActions {

	/**
	 * Open a session connection to the db
	 * @return resource
	 */
	public static function open(){
//		echo 'open'."\n<br>";
		$db = RuntimeInfo::instance()->connections()->MySQL(RuntimeInfo::instance()->helpers()->Session()->getSessionConfig()->getHosts());
		
		// Change this dependant on what the default db type is (ex: mysql 4.o-, 4.1+, or oracle 10g+)
		if($db instanceof MySQLAbstraction) { return true; } // to true from $db
		return false;
	}
	
	/**
	 * Close the database connection and session 
	 * @return results
	 */
	public static function close(){
//		echo 'close'."\n<br>";
//		$db = RuntimeInfo::instance()->connections()->MySQL(RuntimeInfo::instance()->helpers()->Session()->getSessionConfig()->getHosts());
		
		// just because i'm closing a session, why does that mean i need to close the db connection?
// 		if($db instanceof MySQLAbstraction)
// 		{
// 			return $db->close();
// 		}
// 		else { return false; }
	}
	
	/**
	 * Gather data from the session
	 * @param int $id
	 * @return results
	 */
	public static function read($id){
//		echo 'read'.$id."\n<br>";
		$db = RuntimeInfo::instance()->connections()->MySQL(RuntimeInfo::instance()->helpers()->Session()->getSessionConfig()->getHosts());
		$query='SELECT data FROM sessions WHERE id='.$db->escape($id);
		$db->query($query);
		if($db->readRow()) { return $db->row_data['data']; }
		return false;
	}
	
	/**
	 * Write data to the session in the db
	 * @param int $id
	 * @param string $data
	 * @return results
	 */
	public static function write($id, $data){
//		echo 'write'.$id."\n<br>";
		$db = RuntimeInfo::instance()->connections()->MySQL(RuntimeInfo::instance()->helpers()->Session()->getSessionConfig()->getHosts());
		$query='REPLACE INTO sessions VALUES ('.$db->escape($id).','.$db->escape(time()).','.$db->escape($data).')';
		return $db->query($query);
	}
	
	/**
	 * Delete the session from the db
	 * @param int $id
	 * @return results
	 */
	public static function destroy($id){
//		echo 'destroy'.$id."\n<br>";
		$db = RuntimeInfo::instance()->connections()->MySQL(RuntimeInfo::instance()->helpers()->Session()->getSessionConfig()->getHosts());
		$query='DELETE FROM sessions WHERE id='.$db->escape($id);
		return $db->query($query);
	}
	
	/**
	 * Clear all the old data out of the db
	 * @param int $max
	 * @return results
	 */
	public static function clean($max){
//		echo 'clean'.$max."\n<br>";
		$db = RuntimeInfo::instance()->connections()->MySQL(RuntimeInfo::instance()->helpers()->Session()->getSessionConfig()->getHosts());
		$query='DELETE FROM sessions WHERE access < '.$db->escape((time()-$max));
		return $db->query($query);
	}
}
