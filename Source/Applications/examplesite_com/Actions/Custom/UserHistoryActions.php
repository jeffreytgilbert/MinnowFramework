<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserHistoryActions extends Actions{
	
	public static function insertUserHistory(UserHistory $UserHistory){
		return parent::MySQLCreateAction('
			INSERT INTO user_history (
				created_datetime,
				page_title,
				location
			) VALUES (
				:created_datetime,
				:page_title,
				:location
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':page_title' => $UserHistory->getString('page_title'),
				':location' => $UserHistory->getString('location'),
				':user_id' => $UserHistory->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':user_id'
			)
		);
	}
	
	public static function selectByUserHistoryId($user_id){
		// Return one object by primary key selection
		return new UserHistory(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				created_datetime,
				page_title,
				location
			FROM user_history 
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
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserHistoryCollection = new UserHistoryCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				page_title,
				location
			FROM user_history 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserHistory'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserHistoryCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserHistoryCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserHistory', new UserHistory());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserHistoryCollection = new UserHistoryCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				page_title,
				location
			FROM user_history 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserHistory'
		));
		
		foreach($UserHistoryCollection->toArray() as $UserHistory){
			$array = $DataCollection->getObjectByFieldValue('user_id',$UserHistory->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserHistory',$UserHistory);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserHistory(UserHistory $UserHistory){
		return parent::MySQLUpdateAction('
			UPDATE user_history 
			SET page_title=:page_title,
				location=:location
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':page_title' => $UserHistory->getString('page_title'),
				':location' => $UserHistory->getString('location'),
				':user_id' => $UserHistory->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':user_id'
			)
		);
	}
	
	public static function deleteUserHistoryById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_history 
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