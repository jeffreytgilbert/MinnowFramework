<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserRoleActions extends Actions{
	
	public static function insertUserRole(UserRole $UserRole){
		return parent::MySQLCreateAction('
			INSERT INTO user_role (
				role_id,
				created_datetime
			) VALUES (
				:role_id,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':role_id' => $UserRole->getInteger('role_id'),
				':created_datetime' => RIGHT_NOW_GMT,
				':user_id' => $UserRole->getInteger('user_id')
			),
			// which fields are integers
			array(
				':role_id',
				':user_id'
			)
		);
	}
	
	public static function selectByUserRoleId($user_id){
		// Return one object by primary key selection
		return new UserRole(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				role_id,
				created_datetime
			FROM user_role 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are integers
			array(
				':user_id'
			),
			// return as this object collection type
			'UserRole'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserRoleCollection = new UserRoleCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				role_id,
				created_datetime
			FROM user_role 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserRole'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserRoleCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserRoleCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserRole', new UserRole());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserRoleCollection = new UserRoleCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				role_id,
				created_datetime
			FROM user_role 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserRole'
		));
		
		foreach($UserRoleCollection->toArray() as $UserRole){
			$array = $DataCollection->getItemsBy('user_id',$UserRole->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserRole',$UserRole);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserRole(UserRole $UserRole){
		return parent::MySQLUpdateAction('
			UPDATE user_role 
			SET role_id=:role_id
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':role_id' => $UserRole->getInteger('role_id'),
				':user_id' => $UserRole->getInteger('user_id')
			),
			// which fields are integers
			array(
				':role_id',
				':user_id'
			)
		);
	}
	
	public static function deleteUserRoleById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_role 
			WHERE user_id=:user_id',
			// bind data to sql variables
			array(
				':user_id' => (int)$user_id
			),
			// which fields are integers
			array(
				':user_id'
			)
		);
	}

}