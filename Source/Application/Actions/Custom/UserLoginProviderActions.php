<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserLoginProviderActions extends Actions{
	
	public static function insertUserLoginProvider(UserLoginProvider $UserLoginProvider){
		return parent::MySQLCreateAction('
			INSERT INTO user_login_provider (
				created_datetime,
				provider_name,
				login_type,
				is_validation_required
			) VALUES (
				:created_datetime,
				:provider_name,
				:login_type,
				:is_validation_required
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':provider_name' => $UserLoginProvider->getString('provider_name'),
				':login_type' => $UserLoginProvider->getString('login_type'),
				':is_validation_required' => $UserLoginProvider->getBoolean('is_validation_required'),
				':user_login_provider_id' => $UserLoginProvider->getInteger('user_login_provider_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':is_validation_required',
				':user_login_provider_id'
			)
		);
	}
	
	public static function selectByUserLoginProviderId($user_login_provider_id){
		// Return one object by primary key selection
		return new UserLoginProvider(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_login_provider_id,
				created_datetime,
				modified_datetime,
				provider_name,
				login_type,
				is_validation_required
			FROM user_login_provider 
			WHERE user_login_provider_id=:user_login_provider_id',
			// bind data to sql variables
			array(
				':user_login_provider_id' => (int)$user_login_provider_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_provider_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserLoginProviderCollection = new UserLoginProviderCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_login_provider_id,
				created_datetime,
				modified_datetime,
				provider_name,
				login_type,
				is_validation_required
			FROM user_login_provider 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserLoginProvider'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserLoginProviderCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserLoginProviderCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_login_provider_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserLoginProvider', new UserLoginProvider());
			if($DataObject->getInteger('user_login_provider_id') > 0){
				$user_login_provider_ids[] = $DataObject->getInteger('user_login_provider_id');
			}
		}
		
		$user_login_provider_ids = array_unique($user_login_provider_ids);
		
		$UserLoginProviderCollection = new UserLoginProviderCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_login_provider_id,
				created_datetime,
				modified_datetime,
				provider_name,
				login_type,
				is_validation_required
			FROM user_login_provider 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserLoginProvider'
		));
		
		foreach($UserLoginProviderCollection->toArray() as $UserLoginProvider){
			$array = $DataCollection->getObjectByFieldValue('user_login_provider_id',$UserLoginProvider->getInteger('user_login_provider_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserLoginProvider',$UserLoginProvider);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserLoginProvider(UserLoginProvider $UserLoginProvider){
		return parent::MySQLUpdateAction('
			UPDATE user_login_provider 
			SET modified_datetime=:modified_datetime,
				provider_name=:provider_name,
				login_type=:login_type,
				is_validation_required=:is_validation_required
			WHERE user_login_provider_id=:user_login_provider_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':provider_name' => $UserLoginProvider->getString('provider_name'),
				':login_type' => $UserLoginProvider->getString('login_type'),
				':is_validation_required' => $UserLoginProvider->getBoolean('is_validation_required'),
				':user_login_provider_id' => $UserLoginProvider->getInteger('user_login_provider_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':user_login_provider_id'
			)
		);
	}
	
	public static function deleteUserLoginProviderById($user_login_provider_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_login_provider 
			WHERE user_login_provider_id=:user_login_provider_id',
			// bind data to sql variables
			array(
				':user_login_provider_id' => (int)$user_login_provider_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':user_login_provider_id'
			)
		);
	}

}