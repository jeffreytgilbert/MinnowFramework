<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class RoleSettingActions extends Actions{
	
	public static function insertRoleSetting(RoleSetting $RoleSetting){
		return parent::MySQLCreateAction('
			INSERT INTO role_setting (
				setting_id,
				created_datetime
			) VALUES (
				:setting_id,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':setting_id' => $RoleSetting->getInteger('setting_id'),
				':created_datetime' => RIGHT_NOW_GMT,
				':role_id' => $RoleSetting->getInteger('role_id')
			),
			// which fields are integers
			array(
				':setting_id',
				':role_id'
			)
		);
	}
	
	public static function selectByRoleSettingId($role_id){
		// Return one object by primary key selection
		return new RoleSetting(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				role_id,
				setting_id,
				created_datetime
			FROM role_setting 
			WHERE role_id=:role_id',
			// bind data to sql variables
			array(
				':role_id' => (int)$role_id
			),
			// which fields are integers
			array(
				':role_id'
			),
			// return as this object collection type
			'RoleSetting'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$RoleSettingCollection = new RoleSettingCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_id,
				setting_id,
				created_datetime
			FROM role_setting 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'RoleSetting'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$RoleSettingCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $RoleSettingCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$role_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('RoleSetting', new RoleSetting());
			if($DataObject->getInteger('role_id') > 0){
				$role_ids[] = $DataObject->getInteger('role_id');
			}
		}
		
		$role_ids = array_unique($role_ids);
		
		$RoleSettingCollection = new RoleSettingCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_id,
				setting_id,
				created_datetime
			FROM role_setting 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'RoleSetting'
		));
		
		foreach($RoleSettingCollection->toArray() as $RoleSetting){
			$array = $DataCollection->getItemsBy('role_id',$RoleSetting->getInteger('role_id'));
			foreach($array as $DataObject){
				$DataObject->set('RoleSetting',$RoleSetting);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateRoleSetting(RoleSetting $RoleSetting){
		return parent::MySQLUpdateAction('
			UPDATE role_setting 
			SET setting_id=:setting_id
			WHERE role_id=:role_id
			',
			// bind data to sql variables
			array(
				':setting_id' => $RoleSetting->getInteger('setting_id'),
				':role_id' => $RoleSetting->getInteger('role_id')
			),
			// which fields are integers
			array(
				':setting_id',
				':role_id'
			)
		);
	}
	
	public static function deleteRoleSettingById($role_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM role_setting 
			WHERE role_id=:role_id',
			// bind data to sql variables
			array(
				':role_id' => (int)$role_id
			),
			// which fields are integers
			array(
				':role_id'
			)
		);
	}

}