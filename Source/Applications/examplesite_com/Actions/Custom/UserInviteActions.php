<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserInviteActions extends Actions{
	
	public static function insertUserInvite(UserInvite $UserInvite){
		return parent::MySQLCreateAction('
			INSERT INTO user_invite (
				user_id,
				code,
				code_used_by,
				created_datetime
			) VALUES (
				:user_id,
				:code,
				:code_used_by,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':user_id' => $UserInvite->getInteger('user_id'),
				':code' => $UserInvite->getString('code'),
				':code_used_by' => $UserInvite->getInteger('code_used_by'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_invite_id' => $UserInvite->getInteger('user_invite_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':code_used_by',
				':user_invite_id'
			)
		);
	}
	
	public static function selectByUserInviteId($user_invite_id){
		// Return one object by primary key selection
		return new UserInvite(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_invite_id,
				user_id,
				code,
				code_used_by,
				created_datetime,
				modified_datetime
			FROM user_invite 
			WHERE user_invite_id=:user_invite_id',
			// bind data to sql variables
			array(
				':user_invite_id' => (int)$user_invite_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_invite_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserInviteCollection = new UserInviteCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_invite_id,
				user_id,
				code,
				code_used_by,
				created_datetime,
				modified_datetime
			FROM user_invite 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserInvite'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserInviteCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserInviteCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_invite_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserInvite', new UserInvite());
			if($DataObject->getInteger('user_invite_id') > 0){
				$user_invite_ids[] = $DataObject->getInteger('user_invite_id');
			}
		}
		
		$user_invite_ids = array_unique($user_invite_ids);
		
		$UserInviteCollection = new UserInviteCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_invite_id,
				user_id,
				code,
				code_used_by,
				created_datetime,
				modified_datetime
			FROM user_invite 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserInvite'
		));
		
		foreach($UserInviteCollection->toArray() as $UserInvite){
			$array = $DataCollection->getObjectByFieldValue('user_invite_id',$UserInvite->getInteger('user_invite_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserInvite',$UserInvite);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserInvite(UserInvite $UserInvite){
		return parent::MySQLUpdateAction('
			UPDATE user_invite 
			SET user_id=:user_id,
				code=:code,
				code_used_by=:code_used_by,
				modified_datetime=:modified_datetime
			WHERE user_invite_id=:user_invite_id
			',
			// bind data to sql variables
			array(
				':user_id' => $UserInvite->getInteger('user_id'),
				':code' => $UserInvite->getString('code'),
				':code_used_by' => $UserInvite->getInteger('code_used_by'),
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_invite_id' => $UserInvite->getInteger('user_invite_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':code_used_by',
				':user_invite_id'
			)
		);
	}
	
	public static function deleteUserInviteById($user_invite_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_invite 
			WHERE user_invite_id=:user_invite_id',
			// bind data to sql variables
			array(
				':user_invite_id' => (int)$user_invite_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_invite_id'
			)
		);
	}

}