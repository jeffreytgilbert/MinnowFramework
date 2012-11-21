<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class RoleActions extends Actions{
	
	public static function insertRole(Role $Role){
		return parent::MySQLCreateAction('
			INSERT INTO role (
				role_group_id,
				is_user_selectable,
				title,
				hint,
				created_datetime
			) VALUES (
				:role_group_id,
				:is_user_selectable,
				:title,
				:hint,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':role_group_id' => $Role->getInteger('role_group_id'),
				':is_user_selectable' => $Role->getBoolean('is_user_selectable'),
				':title' => $Role->getString('title'),
				':hint' => $Role->getString('hint'),
				':created_datetime' => RIGHT_NOW_GMT,
				':role_id' => $Role->getInteger('role_id')
			),
			// which fields are integers
			array(
				':role_group_id',
				':role_id'
			)
		);
	}
	
	public static function selectByRoleId($role_id){
		// Return one object by primary key selection
		return new Role(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				role_id,
				role_group_id,
				is_user_selectable,
				title,
				hint,
				created_datetime
			FROM role 
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
			'Role'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$RoleCollection = new RoleCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_id,
				role_group_id,
				is_user_selectable,
				title,
				hint,
				created_datetime
			FROM role 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Role'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$RoleCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $RoleCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$role_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Role', new Role());
			if($DataObject->getInteger('role_id') > 0){
				$role_ids[] = $DataObject->getInteger('role_id');
			}
		}
		
		$role_ids = array_unique($role_ids);
		
		$RoleCollection = new RoleCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				role_id,
				role_group_id,
				is_user_selectable,
				title,
				hint,
				created_datetime
			FROM role 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Role'
		));
		
		foreach($RoleCollection->toArray() as $Role){
			$array = $DataCollection->getItemsBy('role_id',$Role->getInteger('role_id'));
			foreach($array as $DataObject){
				$DataObject->set('Role',$Role);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateRole(Role $Role){
		return parent::MySQLUpdateAction('
			UPDATE role 
			SET role_group_id=:role_group_id,
				is_user_selectable=:is_user_selectable,
				title=:title,
				hint=:hint
			WHERE role_id=:role_id
			',
			// bind data to sql variables
			array(
				':role_group_id' => $Role->getInteger('role_group_id'),
				':is_user_selectable' => $Role->getBoolean('is_user_selectable'),
				':title' => $Role->getString('title'),
				':hint' => $Role->getString('hint'),
				':role_id' => $Role->getInteger('role_id')
			),
			// which fields are integers
			array(
				':role_group_id',
				':role_id'
			)
		);
	}
	
	public static function deleteRoleById($role_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM role 
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