<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserLoginProviderActions extends Actions{
	
	public static function insertUserLoginProvider(UserLoginProvider $UserLoginProvider){
		return parent::MySQLCreateAction('
			INSERT INTO user_login_provider (
				provider_name,
				is_validation_required,
				created_datetime
			) VALUES (
				:provider_name,
				:is_validation_required,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':provider_name' => $UserLoginProvider->getString('provider_name'),
				':is_validation_required' => $UserLoginProvider->getBoolean('is_validation_required'),
				':created_datetime' => RIGHT_NOW_GMT,
				':user_login_provider_id' => $UserLoginProvider->getInteger('user_login_provider_id')
			),
			// which fields are integers
			array(
				
				':user_login_provider_id'
			)
		);
	}
	
	public static function selectByUserLoginProviderId($user_login_provider_id){
		// Return one object by primary key selection
		return new UserLoginProvider(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_login_provider_id,
				provider_name,
				is_validation_required,
				created_datetime,
				modified_datetime
			FROM user_login_provider 
			WHERE user_login_provider_id=:user_login_provider_id',
			// bind data to sql variables
			array(
				':user_login_provider_id' => (int)$user_login_provider_id
			),
			// which fields are integers
			array(
				':user_login_provider_id'
			),
			// return as this object collection type
			'UserLoginProvider'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserLoginProviderCollection = new UserLoginProviderCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_login_provider_id,
				provider_name,
				is_validation_required,
				created_datetime,
				modified_datetime
			FROM user_login_provider 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
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
				provider_name,
				is_validation_required,
				created_datetime,
				modified_datetime
			FROM user_login_provider 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserLoginProvider'
		));
		
		foreach($UserLoginProviderCollection->toArray() as $UserLoginProvider){
			$array = $DataCollection->getItemsBy('user_login_provider_id',$UserLoginProvider->getInteger('user_login_provider_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserLoginProvider',$UserLoginProvider);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserLoginProvider(UserLoginProvider $UserLoginProvider){
		return parent::MySQLUpdateAction('
			UPDATE user_login_provider 
			SET provider_name=:provider_name,
				is_validation_required=:is_validation_required,
				modified_datetime=:modified_datetime
			WHERE user_login_provider_id=:user_login_provider_id
			',
			// bind data to sql variables
			array(
				':provider_name' => $UserLoginProvider->getString('provider_name'),
				':is_validation_required' => $UserLoginProvider->getBoolean('is_validation_required'),
				':modified_datetime' => RIGHT_NOW_GMT,
				':user_login_provider_id' => $UserLoginProvider->getInteger('user_login_provider_id')
			),
			// which fields are integers
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
			// which fields are integers
			array(
				':user_login_provider_id'
			)
		);
	}

}