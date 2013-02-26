<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class AchievementActions extends Actions{
	
	public static function insertAchievement(Achievement $Achievement){
		return parent::MySQLCreateAction('
			INSERT INTO achievement (
				created_datetime,
				modifed_datetime,
				title,
				description,
				thumbnail,
				unlocked_actions
			) VALUES (
				:created_datetime,
				:modifed_datetime,
				:title,
				:description,
				:thumbnail,
				:unlocked_actions
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':modifed_datetime' => $Achievement->getDateTimeObject('modifed_datetime')->getMySQLFormat('datetime'),
				':title' => $Achievement->getString('title'),
				':description' => $Achievement->getString('description'),
				':thumbnail' => $Achievement->getString('thumbnail'),
				':unlocked_actions' => $Achievement->getString('unlocked_actions'),
				':achievement_id' => $Achievement->getInteger('achievement_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':achievement_id'
			)
		);
	}
	
	public static function selectByAchievementId($achievement_id){
		// Return one object by primary key selection
		return new Achievement(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				achievement_id,
				created_datetime,
				modifed_datetime,
				title,
				description,
				thumbnail,
				unlocked_actions
			FROM achievement 
			WHERE achievement_id=:achievement_id',
			// bind data to sql variables
			array(
				':achievement_id' => (int)$achievement_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':achievement_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$AchievementCollection = new AchievementCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				achievement_id,
				created_datetime,
				modifed_datetime,
				title,
				description,
				thumbnail,
				unlocked_actions
			FROM achievement 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Achievement'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$AchievementCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $AchievementCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$achievement_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Achievement', new Achievement());
			if($DataObject->getInteger('achievement_id') > 0){
				$achievement_ids[] = $DataObject->getInteger('achievement_id');
			}
		}
		
		$achievement_ids = array_unique($achievement_ids);
		
		$AchievementCollection = new AchievementCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				achievement_id,
				created_datetime,
				modifed_datetime,
				title,
				description,
				thumbnail,
				unlocked_actions
			FROM achievement 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Achievement'
		));
		
		foreach($AchievementCollection->toArray() as $Achievement){
			$array = $DataCollection->getObjectByFieldValue('achievement_id',$Achievement->getInteger('achievement_id'));
			foreach($array as $DataObject){
				$DataObject->set('Achievement',$Achievement);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateAchievement(Achievement $Achievement){
		return parent::MySQLUpdateAction('
			UPDATE achievement 
			SET modifed_datetime=:modifed_datetime,
				title=:title,
				description=:description,
				thumbnail=:thumbnail,
				unlocked_actions=:unlocked_actions
			WHERE achievement_id=:achievement_id
			',
			// bind data to sql variables
			array(
				':modifed_datetime' => $Achievement->getDateTimeObject('modifed_datetime')->getMySQLFormat('datetime'),
				':title' => $Achievement->getString('title'),
				':description' => $Achievement->getString('description'),
				':thumbnail' => $Achievement->getString('thumbnail'),
				':unlocked_actions' => $Achievement->getString('unlocked_actions'),
				':achievement_id' => $Achievement->getInteger('achievement_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				
				':achievement_id'
			)
		);
	}
	
	public static function deleteAchievementById($achievement_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM achievement 
			WHERE achievement_id=:achievement_id',
			// bind data to sql variables
			array(
				':achievement_id' => (int)$achievement_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':achievement_id'
			)
		);
	}

}