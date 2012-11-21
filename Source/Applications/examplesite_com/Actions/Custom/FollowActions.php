<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class FollowActions extends Actions{
	
	public static function insertFollow(Follow $Follow){
		return parent::MySQLCreateAction('
			INSERT INTO follow (
				created_datetime,
				target_user_id
			) VALUES (
				:created_datetime,
				:target_user_id
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':target_user_id' => $Follow->getInteger('target_user_id'),
				':my_user_id' => $Follow->getInteger('my_user_id')
			),
			// which fields are integers
			array(
				':target_user_id',
				':my_user_id'
			)
		);
	}
	
	public static function selectByFollowId($my_user_id){
		// Return one object by primary key selection
		return new Follow(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id
			FROM follow 
			WHERE my_user_id=:my_user_id',
			// bind data to sql variables
			array(
				':my_user_id' => (int)$my_user_id
			),
			// which fields are integers
			array(
				':my_user_id'
			),
			// return as this object collection type
			'Follow'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$FollowCollection = new FollowCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id
			FROM follow 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Follow'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$FollowCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $FollowCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$my_user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Follow', new Follow());
			if($DataObject->getInteger('my_user_id') > 0){
				$my_user_ids[] = $DataObject->getInteger('my_user_id');
			}
		}
		
		$my_user_ids = array_unique($my_user_ids);
		
		$FollowCollection = new FollowCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id
			FROM follow 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Follow'
		));
		
		foreach($FollowCollection->toArray() as $Follow){
			$array = $DataCollection->getItemsBy('my_user_id',$Follow->getInteger('my_user_id'));
			foreach($array as $DataObject){
				$DataObject->set('Follow',$Follow);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateFollow(Follow $Follow){
		return parent::MySQLUpdateAction('
			UPDATE follow 
			SET modified_datetime=:modified_datetime,
				target_user_id=:target_user_id
			WHERE my_user_id=:my_user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':target_user_id' => $Follow->getInteger('target_user_id'),
				':my_user_id' => $Follow->getInteger('my_user_id')
			),
			// which fields are integers
			array(
				':target_user_id',
				':my_user_id'
			)
		);
	}
	
	public static function deleteFollowById($my_user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM follow 
			WHERE my_user_id=:my_user_id',
			// bind data to sql variables
			array(
				':my_user_id' => (int)$my_user_id
			),
			// which fields are integers
			array(
				':my_user_id'
			)
		);
	}

}