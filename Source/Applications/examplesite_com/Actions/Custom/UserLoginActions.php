<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserLoginActions extends Actions{
	
	public static function insertUserLogin(UserLogin $UserLogin){
		return parent::MySQLCreateAction('
			INSERT INTO user_login (
				user_id,
				created_datetime,
				unique_identifier,
				user_login_provider_id,
				serialized_credentials,
				current_failed_attempts,
				total_failed_attempts,
				last_failed_attempt,
				is_verified
			) VALUES (
				:user_id,
				:created_datetime,
				:unique_identifier,
				:user_login_provider_id,
				:serialized_credentials,
				:current_failed_attempts,
				:total_failed_attempts,
				:last_failed_attempt,
				:is_verified
			)',
			// bind data to sql variables
			array(
				':user_id' => $UserLogin->getInteger('user_id'),
				':created_datetime' => RIGHT_NOW_GMT,
				':unique_identifier' => $UserLogin->getString('unique_identifier'),
				':user_login_provider_id' => $UserLogin->getInteger('user_login_provider_id'),
				':serialized_credentials' => $UserLogin->getString('serialized_credentials'),
				':current_failed_attempts' => $UserLogin->getInteger('current_failed_attempts'),
				':total_failed_attempts' => $UserLogin->getInteger('total_failed_attempts'),
				':last_failed_attempt' => $UserLogin->getDateTimeObject('last_failed_attempt')->getMySQLFormat('datetime'),
				':is_verified' => $UserLogin->getBoolean('is_verified'),
				':user_login_id' => $UserLogin->getInteger('user_login_id')
			),
			// which fields are integers
			array(
				':user_id',
				':user_login_provider_id',
				':current_failed_attempts',
				':total_failed_attempts',
				':user_login_id'
			)
		);
	}
	
	public static function selectByUserLoginId($user_login_id){
		// Return one object by primary key selection
		return new UserLogin(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_login_id,
				user_id,
				created_datetime,
				modified_datetime,
				unique_identifier,
				user_login_provider_id,
				serialized_credentials,
				current_failed_attempts,
				total_failed_attempts,
				last_failed_attempt,
				is_verified
			FROM user_login 
			WHERE user_login_id=:user_login_id',
			// bind data to sql variables
			array(
				':user_login_id' => (int)$user_login_id
			),
			// which fields are integers
			array(
				':user_login_id'
			),
			// return as this object collection type
			'UserLogin'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserLoginCollection = new UserLoginCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_login_id,
				user_id,
				created_datetime,
				modified_datetime,
				unique_identifier,
				user_login_provider_id,
				serialized_credentials,
				current_failed_attempts,
				total_failed_attempts,
				last_failed_attempt,
				is_verified
			FROM user_login 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserLogin'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserLoginCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserLoginCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_login_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserLogin', new UserLogin());
			if($DataObject->getInteger('user_login_id') > 0){
				$user_login_ids[] = $DataObject->getInteger('user_login_id');
			}
		}
		
		$user_login_ids = array_unique($user_login_ids);
		
		$UserLoginCollection = new UserLoginCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_login_id,
				user_id,
				created_datetime,
				modified_datetime,
				unique_identifier,
				user_login_provider_id,
				serialized_credentials,
				current_failed_attempts,
				total_failed_attempts,
				last_failed_attempt,
				is_verified
			FROM user_login 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserLogin'
		));
		
		foreach($UserLoginCollection->toArray() as $UserLogin){
			$array = $DataCollection->getItemsBy('user_login_id',$UserLogin->getInteger('user_login_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserLogin',$UserLogin);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserLogin(UserLogin $UserLogin){
		return parent::MySQLUpdateAction('
			UPDATE user_login 
			SET user_id=:user_id,
				modified_datetime=:modified_datetime,
				unique_identifier=:unique_identifier,
				user_login_provider_id=:user_login_provider_id,
				serialized_credentials=:serialized_credentials,
				current_failed_attempts=:current_failed_attempts,
				total_failed_attempts=:total_failed_attempts,
				last_failed_attempt=:last_failed_attempt,
				is_verified=:is_verified
			WHERE user_login_id=:user_login_id
			',
			// bind data to sql variables
			array(
				':user_id' => $UserLogin->getInteger('user_id'),
				':modified_datetime' => RIGHT_NOW_GMT,
				':unique_identifier' => $UserLogin->getString('unique_identifier'),
				':user_login_provider_id' => $UserLogin->getInteger('user_login_provider_id'),
				':serialized_credentials' => $UserLogin->getString('serialized_credentials'),
				':current_failed_attempts' => $UserLogin->getInteger('current_failed_attempts'),
				':total_failed_attempts' => $UserLogin->getInteger('total_failed_attempts'),
				':last_failed_attempt' => $UserLogin->getDateTimeObject('last_failed_attempt')->getMySQLFormat('datetime'),
				':is_verified' => $UserLogin->getBoolean('is_verified'),
				':user_login_id' => $UserLogin->getInteger('user_login_id')
			),
			// which fields are integers
			array(
				':user_id',
				':user_login_provider_id',
				':current_failed_attempts',
				':total_failed_attempts',
				':user_login_id'
			)
		);
	}
	
	public static function deleteUserLoginById($user_login_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_login 
			WHERE user_login_id=:user_login_id',
			// bind data to sql variables
			array(
				':user_login_id' => (int)$user_login_id
			),
			// which fields are integers
			array(
				':user_login_id'
			)
		);
	}

}