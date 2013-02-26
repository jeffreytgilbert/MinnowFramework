<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class SettingActions extends Actions{
	
	public static function insertSetting(Setting $Setting){
		return parent::MySQLCreateAction('
			INSERT INTO setting (
				setting_group_id,
				type,
				name,
				hint,
				default_value,
				is_active,
				is_on_by_default,
				is_shown_at_signup,
				created_datetime
			) VALUES (
				:setting_group_id,
				:type,
				:name,
				:hint,
				:default_value,
				:is_active,
				:is_on_by_default,
				:is_shown_at_signup,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':setting_group_id' => $Setting->getInteger('setting_group_id'),
				':type' => $Setting->getString('type'),
				':name' => $Setting->getString('name'),
				':hint' => $Setting->getString('hint'),
				':default_value' => $Setting->getString('default_value'),
				':is_active' => $Setting->getBoolean('is_active'),
				':is_on_by_default' => $Setting->getBoolean('is_on_by_default'),
				':is_shown_at_signup' => $Setting->getBoolean('is_shown_at_signup'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':setting_id' => $Setting->getInteger('setting_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':setting_group_id',
				':is_active',
				':is_on_by_default',
				':is_shown_at_signup',
				':setting_id'
			)
		);
	}
	
	public static function selectBySettingId($setting_id){
		// Return one object by primary key selection
		return new Setting(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				setting_id,
				setting_group_id,
				type,
				name,
				hint,
				default_value,
				is_active,
				is_on_by_default,
				is_shown_at_signup,
				created_datetime
			FROM setting 
			WHERE setting_id=:setting_id',
			// bind data to sql variables
			array(
				':setting_id' => (int)$setting_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':setting_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$SettingCollection = new SettingCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				setting_id,
				setting_group_id,
				type,
				name,
				hint,
				default_value,
				is_active,
				is_on_by_default,
				is_shown_at_signup,
				created_datetime
			FROM setting 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Setting'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$SettingCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $SettingCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$setting_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Setting', new Setting());
			if($DataObject->getInteger('setting_id') > 0){
				$setting_ids[] = $DataObject->getInteger('setting_id');
			}
		}
		
		$setting_ids = array_unique($setting_ids);
		
		$SettingCollection = new SettingCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				setting_id,
				setting_group_id,
				type,
				name,
				hint,
				default_value,
				is_active,
				is_on_by_default,
				is_shown_at_signup,
				created_datetime
			FROM setting 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Setting'
		));
		
		foreach($SettingCollection->toArray() as $Setting){
			$array = $DataCollection->getObjectByFieldValue('setting_id',$Setting->getInteger('setting_id'));
			foreach($array as $DataObject){
				$DataObject->set('Setting',$Setting);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateSetting(Setting $Setting){
		return parent::MySQLUpdateAction('
			UPDATE setting 
			SET setting_group_id=:setting_group_id,
				type=:type,
				name=:name,
				hint=:hint,
				default_value=:default_value,
				is_active=:is_active,
				is_on_by_default=:is_on_by_default,
				is_shown_at_signup=:is_shown_at_signup
			WHERE setting_id=:setting_id
			',
			// bind data to sql variables
			array(
				':setting_group_id' => $Setting->getInteger('setting_group_id'),
				':type' => $Setting->getString('type'),
				':name' => $Setting->getString('name'),
				':hint' => $Setting->getString('hint'),
				':default_value' => $Setting->getString('default_value'),
				':is_active' => $Setting->getBoolean('is_active'),
				':is_on_by_default' => $Setting->getBoolean('is_on_by_default'),
				':is_shown_at_signup' => $Setting->getBoolean('is_shown_at_signup'),
				':setting_id' => $Setting->getInteger('setting_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':setting_group_id',
				':setting_id'
			)
		);
	}
	
	public static function deleteSettingById($setting_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM setting 
			WHERE setting_id=:setting_id',
			// bind data to sql variables
			array(
				':setting_id' => (int)$setting_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':setting_id'
			)
		);
	}

}