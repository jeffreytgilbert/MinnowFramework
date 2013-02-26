<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserPowerActions extends Actions{
	
	public static function insertUserPower(UserPower $UserPower){
		return parent::MySQLCreateAction('
			INSERT INTO user_power (
				power_id,
				created_datetime
			) VALUES (
				:power_id,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':power_id' => $UserPower->getInteger('power_id'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_id' => $UserPower->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':power_id',
				':user_id'
			)
		);
	}
	
	public static function selectByUserPowerId($user_id){
		// Return one object by primary key selection
		return new UserPower(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				power_id,
				created_datetime
			FROM user_power 
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
		$UserPowerCollection = new UserPowerCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				power_id,
				created_datetime
			FROM user_power 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserPower'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserPowerCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserPowerCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserPower', new UserPower());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserPowerCollection = new UserPowerCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				power_id,
				created_datetime
			FROM user_power 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserPower'
		));
		
		foreach($UserPowerCollection->toArray() as $UserPower){
			$array = $DataCollection->getObjectByFieldValue('user_id',$UserPower->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserPower',$UserPower);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserPower(UserPower $UserPower){
		return parent::MySQLUpdateAction('
			UPDATE user_power 
			SET power_id=:power_id
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':power_id' => $UserPower->getInteger('power_id'),
				':user_id' => $UserPower->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':power_id',
				':user_id'
			)
		);
	}
	
	public static function deleteUserPowerById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_power 
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