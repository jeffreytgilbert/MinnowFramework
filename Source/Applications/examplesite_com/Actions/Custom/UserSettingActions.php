<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserSettingActions extends Actions{
	
	public static function insertUserSetting(UserSetting $UserSetting){
		return parent::MySQLCreateAction('
			INSERT INTO user_setting (
				setting_id,
				value,
				created_datetime
			) VALUES (
				:setting_id,
				:value,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':setting_id' => $UserSetting->getInteger('setting_id'),
				':value' => $UserSetting->getString('value'),
				':created_datetime' => RIGHT_NOW_GMT,
				':user_id' => $UserSetting->getInteger('user_id')
			),
			// which fields are integers
			array(
				':setting_id',
				':user_id'
			)
		);
	}
	
	public static function selectByUserSettingId($user_id){
		// Return one object by primary key selection
		return new UserSetting(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				setting_id,
				value,
				created_datetime,
				modified_datetime
			FROM user_setting 
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
			'UserSetting'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UserSettingCollection = new UserSettingCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				setting_id,
				value,
				created_datetime,
				modified_datetime
			FROM user_setting 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserSetting'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserSettingCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserSettingCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserSetting', new UserSetting());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserSettingCollection = new UserSettingCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				setting_id,
				value,
				created_datetime,
				modified_datetime
			FROM user_setting 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UserSetting'
		));
		
		foreach($UserSettingCollection->toArray() as $UserSetting){
			$array = $DataCollection->getItemsBy('user_id',$UserSetting->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserSetting',$UserSetting);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserSetting(UserSetting $UserSetting){
		return parent::MySQLUpdateAction('
			UPDATE user_setting 
			SET setting_id=:setting_id,
				value=:value,
				modified_datetime=:modified_datetime
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':setting_id' => $UserSetting->getInteger('setting_id'),
				':value' => $UserSetting->getString('value'),
				':modified_datetime' => RIGHT_NOW_GMT,
				':user_id' => $UserSetting->getInteger('user_id')
			),
			// which fields are integers
			array(
				':setting_id',
				':user_id'
			)
		);
	}
	
	public static function deleteUserSettingById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_setting 
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