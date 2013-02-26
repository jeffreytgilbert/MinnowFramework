<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserAchievementActions extends Actions{
	
	public static function insertUserAchievement(UserAchievement $UserAchievement){
		return parent::MySQLCreateAction('
			INSERT INTO user_achievement (
				achievement_id,
				created_datetime
			) VALUES (
				:achievement_id,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':achievement_id' => $UserAchievement->getInteger('achievement_id'),
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':user_id' => $UserAchievement->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':achievement_id',
				':user_id'
			)
		);
	}
	
	public static function selectByUserAchievementId($user_id){
		// Return one object by primary key selection
		return new UserAchievement(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				achievement_id,
				created_datetime
			FROM user_achievement 
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
		$UserAchievementCollection = new UserAchievementCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				achievement_id,
				created_datetime
			FROM user_achievement 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserAchievement'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserAchievementCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserAchievementCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserAchievement', new UserAchievement());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserAchievementCollection = new UserAchievementCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				achievement_id,
				created_datetime
			FROM user_achievement 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserAchievement'
		));
		
		foreach($UserAchievementCollection->toArray() as $UserAchievement){
			$array = $DataCollection->getObjectByFieldValue('user_id',$UserAchievement->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserAchievement',$UserAchievement);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserAchievement(UserAchievement $UserAchievement){
		return parent::MySQLUpdateAction('
			UPDATE user_achievement 
			SET achievement_id=:achievement_id
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':achievement_id' => $UserAchievement->getInteger('achievement_id'),
				':user_id' => $UserAchievement->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':achievement_id',
				':user_id'
			)
		);
	}
	
	public static function deleteUserAchievementById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_achievement 
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