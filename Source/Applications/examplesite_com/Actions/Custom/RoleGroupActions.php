<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class RoleGroupActions extends Actions{
	
	public static function insertRoleGroup(RoleGroup $RoleGroup){
		return parent::MySQLCreateAction('
			INSERT INTO role_group (
				created_datetime,
				group_name,
				is_group_single_role_limited
			) VALUES (
				:created_datetime,
				:group_name,
				:is_group_single_role_limited
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':group_name' => $RoleGroup->getString('group_name'),
				':is_group_single_role_limited' => $RoleGroup->getBoolean('is_group_single_role_limited'),
				':role_group_id' => $RoleGroup->getInteger('role_group_id')
			),
			// which fields are integers
			array(
				
				':role_group_id'
			)
		);
	}
	
	public static function selectByRoleGroupId($role_group_id){
		// Return one object by primary key selection
		return new RoleGroup(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				role_group_id,
				created_datetime,
				group_name,
				is_group_single_role_limited
			FROM role_group 
			WHERE role_group_id=:role_group_id',
			// bind data to sql variables
			array(
				':role_group_id' => (int)$role_group_id
			),
			// which fields are integers
			array(
				':role_group_id'
			),
			// return as this object collection type
			'RoleGroup'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$RoleGroupCollection = new RoleGroupCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_group_id,
				created_datetime,
				group_name,
				is_group_single_role_limited
			FROM role_group 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'RoleGroup'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$RoleGroupCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $RoleGroupCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$role_group_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('RoleGroup', new RoleGroup());
			if($DataObject->getInteger('role_group_id') > 0){
				$role_group_ids[] = $DataObject->getInteger('role_group_id');
			}
		}
		
		$role_group_ids = array_unique($role_group_ids);
		
		$RoleGroupCollection = new RoleGroupCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_group_id,
				created_datetime,
				group_name,
				is_group_single_role_limited
			FROM role_group 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'RoleGroup'
		));
		
		foreach($RoleGroupCollection->toArray() as $RoleGroup){
			$array = $DataCollection->getItemsBy('role_group_id',$RoleGroup->getInteger('role_group_id'));
			foreach($array as $DataObject){
				$DataObject->set('RoleGroup',$RoleGroup);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateRoleGroup(RoleGroup $RoleGroup){
		return parent::MySQLUpdateAction('
			UPDATE role_group 
			SET group_name=:group_name,
				is_group_single_role_limited=:is_group_single_role_limited
			WHERE role_group_id=:role_group_id
			',
			// bind data to sql variables
			array(
				':group_name' => $RoleGroup->getString('group_name'),
				':is_group_single_role_limited' => $RoleGroup->getBoolean('is_group_single_role_limited'),
				':role_group_id' => $RoleGroup->getInteger('role_group_id')
			),
			// which fields are integers
			array(
				
				':role_group_id'
			)
		);
	}
	
	public static function deleteRoleGroupById($role_group_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM role_group 
			WHERE role_group_id=:role_group_id',
			// bind data to sql variables
			array(
				':role_group_id' => (int)$role_group_id
			),
			// which fields are integers
			array(
				':role_group_id'
			)
		);
	}

}