<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserProfileActions extends Actions{
	
	public static function insertUserProfile(UserProfile $UserProfile){
		return parent::MySQLCreateAction('
			INSERT INTO user_profile (
				created_datetime
			) VALUES (
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':user_id' => $UserProfile->getInteger('user_id')
			),
			// which fields are integers
			array(
				
				':user_id'
			)
		);
	}
	
	public static function selectByUserProfileId($user_id){
		// Return one object by primary key selection
		return new UserProfile(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime
			FROM user_profile 
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
			'UserProfile'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserProfileCollection = new UserProfileCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime
			FROM user_profile 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserProfile'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserProfileCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserProfileCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserProfile', new UserProfile());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserProfileCollection = new UserProfileCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime
			FROM user_profile 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserProfile'
		));
		
		foreach($UserProfileCollection->toArray() as $UserProfile){
			$array = $DataCollection->getItemsBy('user_id',$UserProfile->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserProfile',$UserProfile);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserProfile(UserProfile $UserProfile){
		return parent::MySQLUpdateAction('
			UPDATE user_profile 
			SET modified_datetime=:modified_datetime
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':user_id' => $UserProfile->getInteger('user_id')
			),
			// which fields are integers
			array(
				
				':user_id'
			)
		);
	}
	
	public static function deleteUserProfileById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_profile 
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