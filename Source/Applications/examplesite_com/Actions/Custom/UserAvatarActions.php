<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UserAvatarActions extends Actions{
	
	public static function insertUserAvatar(UserAvatar $UserAvatar){
		return parent::MySQLCreateAction('
			INSERT INTO user_avatar (
				created_datetime,
				avatar_part_id,
				layer
			) VALUES (
				:created_datetime,
				:avatar_part_id,
				:layer
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':avatar_part_id' => $UserAvatar->getInteger('avatar_part_id'),
				':layer' => $UserAvatar->getInteger('layer'),
				':user_id' => $UserAvatar->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':avatar_part_id',
				':layer',
				':user_id'
			)
		);
	}
	
	public static function selectByUserAvatarId($user_id){
		// Return one object by primary key selection
		return new UserAvatar(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				avatar_part_id,
				layer
			FROM user_avatar 
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
		$UserAvatarCollection = new UserAvatarCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				avatar_part_id,
				layer
			FROM user_avatar 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserAvatar'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UserAvatarCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UserAvatarCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UserAvatar', new UserAvatar());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$UserAvatarCollection = new UserAvatarCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				modified_datetime,
				avatar_part_id,
				layer
			FROM user_avatar 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'UserAvatar'
		));
		
		foreach($UserAvatarCollection->toArray() as $UserAvatar){
			$array = $DataCollection->getObjectByFieldValue('user_id',$UserAvatar->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('UserAvatar',$UserAvatar);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUserAvatar(UserAvatar $UserAvatar){
		return parent::MySQLUpdateAction('
			UPDATE user_avatar 
			SET modified_datetime=:modified_datetime,
				avatar_part_id=:avatar_part_id,
				layer=:layer
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':avatar_part_id' => $UserAvatar->getInteger('avatar_part_id'),
				':layer' => $UserAvatar->getInteger('layer'),
				':user_id' => $UserAvatar->getInteger('user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':avatar_part_id',
				':layer',
				':user_id'
			)
		);
	}
	
	public static function deleteUserAvatarById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM user_avatar 
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