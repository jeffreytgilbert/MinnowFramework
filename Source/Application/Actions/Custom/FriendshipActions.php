<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class FriendshipActions extends Actions{
	
	public static function insertFriendship(Friendship $Friendship){
		return parent::MySQLCreateAction('
			INSERT INTO friendship (
				created_datetime,
				target_user_id,
				order_id,
				note
			) VALUES (
				:created_datetime,
				:target_user_id,
				:order_id,
				:note
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':target_user_id' => $Friendship->getInteger('target_user_id'),
				':order_id' => $Friendship->getInteger('order_id'),
				':note' => $Friendship->getString('note'),
				':my_user_id' => $Friendship->getInteger('my_user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':target_user_id',
				':order_id',
				':my_user_id'
			)
		);
	}
	
	public static function selectByFriendshipId($my_user_id){
		// Return one object by primary key selection
		return new Friendship(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				order_id,
				note
			FROM friendship 
			WHERE my_user_id=:my_user_id',
			// bind data to sql variables
			array(
				':my_user_id' => (int)$my_user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':my_user_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$FriendshipCollection = new FriendshipCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				order_id,
				note
			FROM friendship 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Friendship'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$FriendshipCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $FriendshipCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$my_user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Friendship', new Friendship());
			if($DataObject->getInteger('my_user_id') > 0){
				$my_user_ids[] = $DataObject->getInteger('my_user_id');
			}
		}
		
		$my_user_ids = array_unique($my_user_ids);
		
		$FriendshipCollection = new FriendshipCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				order_id,
				note
			FROM friendship 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Friendship'
		));
		
		foreach($FriendshipCollection->toArray() as $Friendship){
			$array = $DataCollection->getObjectByFieldValue('my_user_id',$Friendship->getInteger('my_user_id'));
			foreach($array as $DataObject){
				$DataObject->set('Friendship',$Friendship);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateFriendship(Friendship $Friendship){
		return parent::MySQLUpdateAction('
			UPDATE friendship 
			SET modified_datetime=:modified_datetime,
				target_user_id=:target_user_id,
				order_id=:order_id,
				note=:note
			WHERE my_user_id=:my_user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':target_user_id' => $Friendship->getInteger('target_user_id'),
				':order_id' => $Friendship->getInteger('order_id'),
				':note' => $Friendship->getString('note'),
				':my_user_id' => $Friendship->getInteger('my_user_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':target_user_id',
				':order_id',
				':my_user_id'
			)
		);
	}
	
	public static function deleteFriendshipById($my_user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM friendship 
			WHERE my_user_id=:my_user_id',
			// bind data to sql variables
			array(
				':my_user_id' => (int)$my_user_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':my_user_id'
			)
		);
	}

}