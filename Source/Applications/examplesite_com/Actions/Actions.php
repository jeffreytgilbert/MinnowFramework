<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
*/

// This actions class is meant to serve as a Helper for Models / Controllers (like a command is for CakePHP, but more tailored to a data type in most cases)
// This is legacy functionality, and will be removed in lieu of Trait based Connector methods which do the same things but in the Connector definition, not in some actions class


abstract class Actions{
	
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
	
	protected static function S3PublicFileUpload($local_file_path, $file_name){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->putObject(
				$s3->inputFile($local_file_path),
				RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'public_bucket_name'),
				$file_name,
				S3::ACL_PUBLIC_READ
			);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3PrivateFileUpload($local_file_path, $file_name){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->putObject(
				$s3->inputFile($local_file_path), 
				RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'private_bucket_name'), 
				$file_name, 
				S3::ACL_PRIVATE
			);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetBucketList($return_object_type='Model'){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			$buckets = $s3->listBuckets();
			
			pr($buckets);die;
			
			return $BucketCollection;
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetBucket($bucket_name,$return_object_type='Model'){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->getBucket($bucket_name);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetPublicFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->getObject(RuntimeInfo::instance()->getApplicationName().'-public', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3GetPrivateFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->getObject(RuntimeInfo::instance()->getApplicationName().'-private', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3DeletePublicFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->deleteObject(RuntimeInfo::instance()->getApplicationName().'-public', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function S3DeletePrivateFile($uri){
		try{
			$s3 = RuntimeInfo::instance()->s3();
			return $s3->deleteObject(RuntimeInfo::instance()->getApplicationName().'-private', $uri);
		} catch(S3Exception $e){
			if(RuntimeInfo::instance()->config('aws', $s3->getConnectionName(), 'debug')){ pr($e); }
			return false;
		}
	}
	
	protected static function MySQLCreateAction($command, $data=array(), $integer_fields=array()){
		static $mysql_actions = array();
		if(!isset($mysql_actions[$command]) || !$mysql_actions[$command] instanceOf MySQLRequest){
			$mysql_actions[$command] = new MySQLRequest($command);
		}
		return $mysql_actions[$command]->runAndReturnId($data, $integer_fields);
	}
	
	protected static function MySQLReadReturnSingleResultAsArrayAction($command, $data=array(), $integer_fields=array()){
		static $mysql_actions = array();
		
		if(!isset($mysql_actions[$command.'SingleResult']) || !$mysql_actions[$command.'SingleResult'] instanceOf MySQLRequest){
			$mysql_actions[$command.'SingleResult'] = new MySQLRequest($command);
		} else {
			$mysql_actions[$command.'SingleResult']->reconstruct();
		}
		
		$return = $mysql_actions[$command.'SingleResult']->runAndReturnOneRow($data,$integer_fields);
		return $return;
	}
	
	protected static function MySQLReadReturnUnmappedAction($command, $data=array(), $integer_fields=array()){
		static $mysql_actions = array();
		
		if(!isset($mysql_actions[$command.'raw']) || !$mysql_actions[$command.'raw'] instanceOf MySQLRequest){
			$mysql_actions[$command.'raw'] = new MySQLRequest($command);
		} else {
			$mysql_actions[$command.'raw']->reconstruct();
		}
		
		$return = $mysql_actions[$command.'raw']->runAndReturnRawData($data,$integer_fields);
		return $return;
	}
	
	protected static function MySQLReadReturnArrayOfObjectsAction($command, $data=array(), $integer_fields=array(), $return_object_type='Model', PagingConfig $PagingConfig=null, Array $map=array()){
		static $mysql_actions = array();
		
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
		
		if(!isset($mysql_actions[$command.'ArrayOfObjects']) || !$mysql_actions[$command.'ArrayOfObjects'] instanceOf MySQLRequest){
			$mysql_actions[$command.'ArrayOfObjects'] = new MySQLRequest($command, $return_object_type, $map);
		} else {
			$mysql_actions[$command.'ArrayOfObjects']->reconstruct($return_object_type);
		}
		
		return $mysql_actions[$command.'ArrayOfObjects']->runAndReturnMappedDataArray($data,$integer_fields);
	}
	
	protected static function MySQLReadReturnObjectCollectionAction($command, $data=array(), $integer_fields=array(), $return_object_type='Model', PagingConfig $PagingConfig=null, Array $map=array()){
		static $mysql_actions = array();
		
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
		
		if(!isset($mysql_actions[$command.'ObjectCollection']) || !$mysql_actions[$command.'ObjectCollection'] instanceOf MySQLRequest){
			$mysql_actions[$command.'ObjectCollection'] = new MySQLRequest($command, $return_object_type, $map);
		} else {
			$mysql_actions[$command.'ObjectCollection']->reconstruct($return_object_type);
		}
		$ResultCollection = $mysql_actions[$command.'ObjectCollection']->runAndReturnMappedDataCollection($data,$integer_fields);
		
		if($PagingConfig instanceof PagingConfig){
			$ResultCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		return $ResultCollection;
	}
	
	/*
	 * Shorthand method for easily dumping things into data collections
	 */
	protected static function MySQLReadAction($command, $data=array(), $integer_fields=array(), $return_object_type='Model', PagingConfig $PagingConfig=null, Array $map=array()){
		return self::MySQLReadReturnObjectCollectionAction($command, $data, $integer_fields, $return_object_type, $PagingConfig, $map);
	}
	
	protected static function MySQLUpdateAction($command, $data=array(), $integer_fields=array()){
		static $mysql_actions = array();
		if(!isset($mysql_actions[$command]) || !$mysql_actions[$command] instanceOf MySQLRequest){
			$mysql_actions[$command] = new MySQLRequest($command);
		}
		return $mysql_actions[$command]->runAndReturnAffectedRows($data,$integer_fields);
	}
	
	protected static function MySQLTotalRows(){
		$db = RuntimeInfo::instance()->mysql();
		$query='SELECT FOUND_ROWS()';
		$db->query($query,__LINE__,__FILE__); 
		$db->readRow('NUM');
		return (int)$db->row_data[0];
	}
	
	/**
	 * @param string $string
	 * @param const $sql_connection_escape_type Ex: SQLConnection::LIKE_WC_FIRST
	 * @return string
	 */
	protected static function MySQLEscapeLikeWildCard($string, $sql_connection_escape_type){
		$db = RuntimeInfo::instance()->mysql();
		return $db->like($string,$sql_connection_escape_type);
	}
}