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
				is_verified
			) VALUES (
				:user_id,
				:created_datetime,
				:unique_identifier,
				:user_login_provider_id,
				:serialized_credentials,
				:is_verified
			)',
			// bind data to sql variables
			array(
				':user_id' => $UserLogin->getInteger('user_id'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':unique_identifier' => $UserLogin->getString('unique_identifier'),
				':user_login_provider_id' => $UserLogin->getInteger('user_login_provider_id'),
				':serialized_credentials' => $UserLogin->getString('serialized_credentials'),
				':is_verified' => $UserLogin->getBoolean('is_verified')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':is_verified'
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
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_id'
			)
		));
	}
	
	public static function selectListByUserId($user_id){
		if(empty($user_id)) { return new UserLoginCollection(); }
		
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
			WHERE user_id = :user_id
			',
			// bind data to sql variables
			array(
				':user_id'=>$user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id'
			),
			'UserLogin'
		));
		
		return $UserLoginCollection;
	}
	
	public static function selectListByUniqueIdentifierAndProviderTypeId($unique_identifier, $provider_type_id){
		
		// Return an object collection
		$UserLogin = new UserLogin(parent::MySQLReadReturnSingleResultAsArrayAction('
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
			WHERE 
				unique_identifier=:unique_identifier AND 
				user_login_provider_id=:user_login_provider_id
			',
			// bind data to sql variables
			array(
				':unique_identifier' => $unique_identifier,
				':user_login_provider_id' => $provider_type_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_provider_id'
			)
		));
		
		return $UserLogin;
	}
	
	public static function selectListByUniqueIdentifiers(Array $unique_identifiers){
		if(count($unique_identifiers) == 0) { return new UserLoginCollection(); }
		
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
			WHERE unique_identifier IN '.MySQLAbstraction::strings($unique_identifiers).'
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserLogin'
		));
		
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
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserLogin'
		));
		
		foreach($UserLoginCollection->toArray() as $UserLogin){
			$array = $DataCollection->getObjectByFieldValue('user_login_id',$UserLogin->getInteger('user_login_id'));
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
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':unique_identifier' => $UserLogin->getString('unique_identifier'),
				':user_login_provider_id' => $UserLogin->getInteger('user_login_provider_id'),
				':serialized_credentials' => $UserLogin->getString('serialized_credentials'),
				':current_failed_attempts' => $UserLogin->getInteger('current_failed_attempts'),
				':total_failed_attempts' => $UserLogin->getInteger('total_failed_attempts'),
				':last_failed_attempt' => $UserLogin->getDateTimeObject('last_failed_attempt')->getMySQLFormat('datetime'),
				':is_verified' => $UserLogin->getBoolean('is_verified'),
				':user_login_id' => $UserLogin->getInteger('user_login_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_id',
				':user_login_provider_id',
				':current_failed_attempts',
				':total_failed_attempts',
				':user_login_id'
			)
		);
	}
	
	public static function resetFailedAttemptCounter($user_login_id){
		return parent::MySQLUpdateAction('
			UPDATE user_login 
			SET modified_datetime=:modified_datetime,
				current_failed_attempts=0
			WHERE user_login_id=:user_login_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_login_id' => $user_login_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_id'
			)
		);
	}

	public static function iterateAttemptCount($user_login_id){
		return parent::MySQLUpdateAction('
			UPDATE user_login 
			SET modified_datetime=:modified_datetime,
				current_failed_attempts=current_failed_attempts+1,
				total_failed_attempts=total_failed_attempts+1
			WHERE user_login_id=:user_login_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_login_id' => $user_login_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
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
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_id'
			)
		);
	}

}