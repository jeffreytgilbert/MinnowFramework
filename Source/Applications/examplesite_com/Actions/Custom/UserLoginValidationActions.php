<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserLoginValidationActions extends Actions{
	
	public static function insertUserLoginValidation(UserLoginValidation $UserLoginValidation){
		return parent::MySQLCreateAction('
			INSERT INTO user_login_validation (
				user_id,
				created_datetime,
				code
			) VALUES (
				:user_id,
				:created_datetime,
				:code
			)',
			// bind data to sql variables
			array(
				':user_id' => $UserLoginValidation->getInteger('user_id'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':code' => $UserLoginValidation->getString('code'),
				':user_login_id' => $UserLoginValidation->getInteger('user_login_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':user_login_id'
			)
		);
	}
	
	public static function selectByUserLoginValidationId($user_login_id){
		// Return one object by primary key selection
		return new UserLoginValidation(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_login_id,
				user_id,
				created_datetime,
				modified_datetime,
				code
			FROM user_login_validation 
			WHERE user_login_id=:user_login_id',
			// bind data to sql variables
			array(
				':user_login_id' => (int)$user_login_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserLoginValidationCollection = new UserLoginValidationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_login_id,
				user_id,
				created_datetime,
				modified_datetime,
				code
			FROM user_login_validation 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserLoginValidation'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserLoginValidationCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserLoginValidationCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_login_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserLoginValidation', new UserLoginValidation());
			if($DataObject->getInteger('user_login_id') > 0){
				$user_login_ids[] = $DataObject->getInteger('user_login_id');
			}
		}
		
		$user_login_ids = array_unique($user_login_ids);
		
		$UserLoginValidationCollection = new UserLoginValidationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_login_id,
				user_id,
				created_datetime,
				modified_datetime,
				code
			FROM user_login_validation 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserLoginValidation'
		));
		
		foreach($UserLoginValidationCollection->toArray() as $UserLoginValidation){
			$array = $DataCollection->getObjectByFieldValue('user_login_id',$UserLoginValidation->getInteger('user_login_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserLoginValidation',$UserLoginValidation);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserLoginValidation(UserLoginValidation $UserLoginValidation){
		return parent::MySQLUpdateAction('
			UPDATE user_login_validation 
			SET user_id=:user_id,
				modified_datetime=:modified_datetime,
				code=:code
			WHERE user_login_id=:user_login_id
			',
			// bind data to sql variables
			array(
				':user_id' => $UserLoginValidation->getInteger('user_id'),
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':code' => $UserLoginValidation->getString('code'),
				':user_login_id' => $UserLoginValidation->getInteger('user_login_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':user_login_id'
			)
		);
	}
	
	public static function deleteUserLoginValidationById($user_login_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_login_validation 
			WHERE user_login_id=:user_login_id',
			// bind data to sql variables
			array(
				':user_login_id' => (int)$user_login_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_id'
			)
		);
	}

}