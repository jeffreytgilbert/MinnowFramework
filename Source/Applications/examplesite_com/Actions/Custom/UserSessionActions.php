<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserSessionActions extends Actions{
	
	public static function insertUserSession(UserSession $UserSession){
		return parent::MySQLCreateAction('
			INSERT INTO user_session (
				user_id, 
				created_datetime, 
				last_access, 
				ip, 
				proxy, 
				user_agent, 
				access_token, 
				php_session_id
			) VALUES (
				:user_id, 
				:created_datetime, 
				:last_access, 
				:ip, 
				:proxy, 
				:user_agent, 
				:access_token, 
				:php_session_id
			)',
			// bind data to sql variables
			array(
				':user_id' => $UserSession->getInteger('user_id'),
				':created_datetime' => $UserSession->getDateTimeObject('created_datetime')->getMySQLFormat('datetime'),
				':last_access' => $UserSession->getDateTimeObject('last_access')->getMySQLFormat('datetime'),
				':ip' => $UserSession->getString('ip'),
				':proxy' => $UserSession->getString('proxy'),
				':user_agent' => $UserSession->getString('unread_messages'),
				':access_token' => $UserSession->getString('access_token'),
				':php_session_id' => $UserSession->getString('php_session_id'),
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':unread_messages',
				':user_id'
			)
		);
	}
	
	public static function selectByUserId($user_id){
		// Return one object by primary key selection
		return new UserSession(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id, 
				created_datetime, 
				last_access, 
				ip, 
				proxy, 
				user_agent, 
				access_token, 
				php_session_id
			FROM user_session 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		));
	}
	
	public static function selectByAccessToken($access_token, $user_id){
		// Return one object by primary key selection
		return new UserSession(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id, 
				created_datetime, 
				last_access, 
				ip, 
				proxy, 
				user_agent, 
				access_token, 
				php_session_id
			FROM user_session 
			WHERE 
				access_token=:access_token AND 
				user_id=:user_id
			LIMIT 1',
			// bind data to sql variables
			array(
				':access_token' => $access_token,
				':user_id' => (int)$user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		));
	}
	
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserSessionCollection = new UserSessionCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id, 
				created_datetime, 
				last_access, 
				ip, 
				proxy, 
				user_agent, 
				access_token, 
				php_session_id
			FROM user_session 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserSession'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserSessionCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserSessionCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserSession', new UserSession());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserSessionCollection = new UserSessionCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				login_time,
				last_access,
				ip,
				proxy,
				unread_messages
			FROM user_session 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserSession'
		));
		
		foreach($UserSessionCollection->toArray() as $UserSession){
			$array = $DataCollection->getObjectByFieldValue('user_id',$UserSession->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserSession',$UserSession);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserSession(UserSession $UserSession){
		return parent::MySQLUpdateAction('
			UPDATE user_session 
			SET user_id=:user_id, 
				created_datetime=:created_datetime, 
				last_access=:last_access, 
				ip=:ip, 
				proxy=:proxy, 
				user_agent=:user_agent, 
				access_token=:access_token, 
				php_session_id=:php_session_id
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':user_id' => $UserSession->getInteger('user_id'),
				':created_datetime' => $UserSession->getDateTimeObject('created_datetime')->getMySQLFormat('datetime'),
				':last_access' => $UserSession->getDateTimeObject('last_access')->getMySQLFormat('datetime'),
				':ip' => $UserSession->getString('ip'),
				':proxy' => $UserSession->getString('proxy'),
				':user_agent' => $UserSession->getInteger('user_agent'),
				':access_token' => $UserSession->getInteger('access_token'),
				':php_session_id' => $UserSession->getInteger('php_session_id'),
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		);
	}
	
	public static function deleteUserSessionByPhpSessionId($php_session_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_session 
			WHERE php_session_id=:php_session_id',
			// bind data to sql variables
			array(
				':php_session_id' => $php_session_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':php_session_id'
			)
		);
	}

	public static function deleteUserSessionsByUserId($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_session 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			)
		);
	}

}