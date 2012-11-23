<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class SettingGroupActions extends Actions{
	
	public static function insertSettingGroup(SettingGroup $SettingGroup){
		return parent::MySQLCreateAction('
			INSERT INTO setting_group (
				created_datetime,
				group_name
			) VALUES (
				:created_datetime,
				:group_name
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':group_name' => $SettingGroup->getString('group_name'),
				':setting_group_id' => $SettingGroup->getInteger('setting_group_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':setting_group_id'
			)
		);
	}
	
	public static function selectBySettingGroupId($setting_group_id){
		// Return one object by primary key selection
		return new SettingGroup(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				setting_group_id,
				created_datetime,
				group_name
			FROM setting_group 
			WHERE setting_group_id=:setting_group_id',
			// bind data to sql variables
			array(
				':setting_group_id' => (int)$setting_group_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':setting_group_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$SettingGroupCollection = new SettingGroupCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				setting_group_id,
				created_datetime,
				group_name
			FROM setting_group 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'SettingGroup'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$SettingGroupCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $SettingGroupCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$setting_group_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('SettingGroup', new SettingGroup());
			if($DataObject->getInteger('setting_group_id') > 0){
				$setting_group_ids[] = $DataObject->getInteger('setting_group_id');
			}
		}
		
		$setting_group_ids = array_unique($setting_group_ids);
		
		$SettingGroupCollection = new SettingGroupCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				setting_group_id,
				created_datetime,
				group_name
			FROM setting_group 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'SettingGroup'
		));
		
		foreach($SettingGroupCollection->toArray() as $SettingGroup){
			$array = $DataCollection->getObjectByFieldValue('setting_group_id',$SettingGroup->getInteger('setting_group_id'));
			foreach($array as $DataObject){
				$DataObject->set('SettingGroup',$SettingGroup);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateSettingGroup(SettingGroup $SettingGroup){
		return parent::MySQLUpdateAction('
			UPDATE setting_group 
			SET group_name=:group_name
			WHERE setting_group_id=:setting_group_id
			',
			// bind data to sql variables
			array(
				':group_name' => $SettingGroup->getString('group_name'),
				':setting_group_id' => $SettingGroup->getInteger('setting_group_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':setting_group_id'
			)
		);
	}
	
	public static function deleteSettingGroupById($setting_group_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM setting_group 
			WHERE setting_group_id=:setting_group_id',
			// bind data to sql variables
			array(
				':setting_group_id' => (int)$setting_group_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':setting_group_id'
			)
		);
	}

}