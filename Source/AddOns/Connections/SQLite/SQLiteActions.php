<?php

trait SQLiteActions{

	protected static function SQLiteCreateAction($command, $data=array(), $integer_fields=array()){
		static $sqlite_actions = array();
		if(!isset($sqlite_actions[$command]) || !$sqlite_actions[$command] instanceOf SQLiteRequest){
			$sqlite_actions[$command] = new SQLiteRequest($command);
		}
		return $sqlite_actions[$command]->runAndReturnId($data, $integer_fields);
	}
	
	protected static function SQLiteReadReturnSingleResultAsArrayAction($command, $data=array(), $integer_fields=array()){
		static $sqlite_actions = array();
	
		if(!isset($sqlite_actions[$command.'SingleResult']) || !$sqlite_actions[$command.'SingleResult'] instanceOf SQLiteRequest){
			$sqlite_actions[$command.'SingleResult'] = new SQLiteRequest($command);
		} else {
			$sqlite_actions[$command.'SingleResult']->reconstruct();
		}
	
		$return = $sqlite_actions[$command.'SingleResult']->runAndReturnOneRow($data,$integer_fields);
		return $return;
	}
	
	protected static function SQLiteReadReturnUnmappedAction($command, $data=array(), $integer_fields=array()){
		static $sqlite_actions = array();
	
		if(!isset($sqlite_actions[$command.'raw']) || !$sqlite_actions[$command.'raw'] instanceOf SQLiteRequest){
			$sqlite_actions[$command.'raw'] = new SQLiteRequest($command);
		} else {
			$sqlite_actions[$command.'raw']->reconstruct();
		}
	
		$return = $sqlite_actions[$command.'raw']->runAndReturnRawData($data,$integer_fields);
		return $return;
	}
	
	protected static function SQLiteReadReturnArrayOfObjectsAction($command, $data=array(), $integer_fields=array(), $return_object_type='Model', PagingConfig $PagingConfig=null, Array $map=array()){
		static $sqlite_actions = array();
	
		if($PagingConfig instanceof PagingConfig && $PagingConfig->isQueryUpdatingOk()){
			$position_of_select = stripos($command, 'select ');
			if($position_of_select === false){
				$command = trim($command);
				if('select' === lower(substr($command,0,6))){
					$position_of_select = 0;
				} else {
					$position_of_select = false;
				}
			}
				
			if($position_of_select !== false){
				$command = substr($command, 0, $position_of_select+6)
				.' SQL_CALC_FOUND_ROWS '
						.substr($command, $position_of_select+6)
						.' LIMIT '.$PagingConfig->getStart().', '.$PagingConfig->getLimit();
			} else {
				die('A query was run with paging configured, but query was not identified as a SELECT statement.
					In some circumstances (such as stored procedures) paging cannot be used with this method.
					Please reconfigure the PagingConfig or query for this call.');
			}
		}
	
		if(!isset($sqlite_actions[$command.'ArrayOfObjects']) || !$sqlite_actions[$command.'ArrayOfObjects'] instanceOf SQLiteRequest){
			$sqlite_actions[$command.'ArrayOfObjects'] = new SQLiteRequest($command, $return_object_type, $map);
		} else {
			$sqlite_actions[$command.'ArrayOfObjects']->reconstruct($return_object_type);
		}
	
		return $sqlite_actions[$command.'ArrayOfObjects']->runAndReturnMappedDataArray($data,$integer_fields);
	}
	
	protected static function SQLiteReadReturnObjectCollectionAction($command, $data=array(), $integer_fields=array(), $return_object_type='Model', PagingConfig $PagingConfig=null, Array $map=array()){
		static $sqlite_actions = array();
	
		if($PagingConfig instanceof PagingConfig && $PagingConfig->isQueryUpdatingOk()){
			$position_of_select = stripos($command, 'select ');
			if($position_of_select === false){
				$command = trim($command);
				if('select' === lower(substr($command,0,6))){
					$position_of_select = 0;
				} else {
					$position_of_select = false;
				}
			}
				
			if($position_of_select !== false){
				$command = substr($command, 0, $position_of_select+6)
				.' SQL_CALC_FOUND_ROWS '
						.substr($command, $position_of_select+6)
						.' LIMIT '.$PagingConfig->getStart().', '.$PagingConfig->getLimit();
			} else {
				die('A query was run with paging configured, but query was not identified as a SELECT statement.
					In some circumstances (such as stored procedures) paging cannot be used with this method.
					Please reconfigure the PagingConfig or query for this call.');
			}
		}
	
		if(!isset($sqlite_actions[$command.'ObjectCollection']) || !$sqlite_actions[$command.'ObjectCollection'] instanceOf SQLiteRequest){
			$sqlite_actions[$command.'ObjectCollection'] = new SQLiteRequest($command, $return_object_type, $map);
		} else {
			$sqlite_actions[$command.'ObjectCollection']->reconstruct($return_object_type);
		}
		$ResultCollection = $sqlite_actions[$command.'ObjectCollection']->runAndReturnMappedDataCollection($data,$integer_fields);
	
		if($PagingConfig instanceof PagingConfig){
			$ResultCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::SQLiteTotalRows());
		}
		return $ResultCollection;
	}
	
	/*
	 * Shorthand method for easily dumping things into data collections
	*/
	protected static function SQLiteReadAction($command, $data=array(), $integer_fields=array(), $return_object_type='Model', PagingConfig $PagingConfig=null, Array $map=array()){
		return self::SQLiteReadReturnObjectCollectionAction($command, $data, $integer_fields, $return_object_type, $PagingConfig, $map);
	}
	
	protected static function SQLiteUpdateAction($command, $data=array(), $integer_fields=array()){
		static $sqlite_actions = array();
		if(!isset($sqlite_actions[$command]) || !$sqlite_actions[$command] instanceOf SQLiteRequest){
			$sqlite_actions[$command] = new SQLiteRequest($command);
		}
		return $sqlite_actions[$command]->runAndReturnAffectedRows($data,$integer_fields);
	}
	
	// 100% positive this doesnt work currently as scripted here since this is mysql code
// 	protected static function SQLiteTotalRows(){
// 		$db = RuntimeInfo::instance()->connections()->SQLite();
// 		$query='SELECT FOUND_ROWS()';
// 		$db->query($query);
// 		$db->readRow('NUM');
// 		return (int)$db->row_data[0];
// 	}
	
	/**
	 * @param string $string
	 * @param const $sql_connection_escape_type Ex: SQLConnection::LIKE_WC_FIRST
	 * @return string
	 */
	protected static function SQLiteEscapeLikeWildCard($string, $sql_connection_escape_type){
		$db = RuntimeInfo::instance()->connections()->SQLite();
		return $db->like($string,$sql_connection_escape_type);
	}
}

