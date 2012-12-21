<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class OnlineMemberActions extends Actions{
	
	public static function insertOnlineMember(OnlineMember $OnlineMember){
		return parent::MySQLCreateAction('
			REPLACE INTO online_member (
				user_id,
				last_active
			) VALUES (
				:user_id,
				:last_active
			)',
			// bind data to sql variables
			array(
				':last_active' => $OnlineMember->getDateTimeObject('last_active')->getMySQLFormat('datetime'),
				':user_id' => $OnlineMember->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':user_id'
			)
		);
	}
	
	public static function selectByOnlineMemberId($user_id){
		// Return one object by primary key selection
		return new OnlineMember(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				last_active
			FROM online_member 
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
		$OnlineMemberCollection = new OnlineMemberCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				last_active
			FROM online_member 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'OnlineMember'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$OnlineMemberCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $OnlineMemberCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('OnlineMember', new OnlineMember());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$OnlineMemberCollection = new OnlineMemberCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				last_active
			FROM online_member 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'OnlineMember'
		));
		
		foreach($OnlineMemberCollection->toArray() as $OnlineMember){
			$array = $DataCollection->getObjectByFieldValue('user_id',$OnlineMember->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('OnlineMember',$OnlineMember);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateOnlineMember(OnlineMember $OnlineMember){
		return parent::MySQLUpdateAction('
			UPDATE online_member 
			SET last_active=:last_active
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':last_active' => $OnlineMember->getDateTimeObject('last_active')->getMySQLFormat('datetime'),
				':user_id' => $OnlineMember->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':user_id'
			)
		);
	}
	
	public static function deleteOnlineMemberById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM online_member 
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