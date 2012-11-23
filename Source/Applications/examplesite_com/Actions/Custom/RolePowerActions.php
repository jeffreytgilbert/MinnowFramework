<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class RolePowerActions extends Actions{
	
	public static function insertRolePower(RolePower $RolePower){
		return parent::MySQLCreateAction('
			INSERT INTO role_power (
				power_id,
				created_datetime
			) VALUES (
				:power_id,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':power_id' => $RolePower->getInteger('power_id'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':role_id' => $RolePower->getInteger('role_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':power_id',
				':role_id'
			)
		);
	}
	
	public static function selectByRolePowerId($role_id){
		// Return one object by primary key selection
		return new RolePower(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				role_id,
				power_id,
				created_datetime
			FROM role_power 
			WHERE role_id=:role_id',
			// bind data to sql variables
			array(
				':role_id' => (int)$role_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':role_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$RolePowerCollection = new RolePowerCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_id,
				power_id,
				created_datetime
			FROM role_power 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'RolePower'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$RolePowerCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $RolePowerCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$role_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('RolePower', new RolePower());
			if($DataObject->getInteger('role_id') > 0){
				$role_ids[] = $DataObject->getInteger('role_id');
			}
		}
		
		$role_ids = array_unique($role_ids);
		
		$RolePowerCollection = new RolePowerCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_id,
				power_id,
				created_datetime
			FROM role_power 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'RolePower'
		));
		
		foreach($RolePowerCollection->toArray() as $RolePower){
			$array = $DataCollection->getObjectByFieldValue('role_id',$RolePower->getInteger('role_id'));
			foreach($array as $DataObject){
				$DataObject->set('RolePower',$RolePower);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateRolePower(RolePower $RolePower){
		return parent::MySQLUpdateAction('
			UPDATE role_power 
			SET power_id=:power_id
			WHERE role_id=:role_id
			',
			// bind data to sql variables
			array(
				':power_id' => $RolePower->getInteger('power_id'),
				':role_id' => $RolePower->getInteger('role_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':power_id',
				':role_id'
			)
		);
	}
	
	public static function deleteRolePowerById($role_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM role_power 
			WHERE role_id=:role_id',
			// bind data to sql variables
			array(
				':role_id' => (int)$role_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':role_id'
			)
		);
	}

}