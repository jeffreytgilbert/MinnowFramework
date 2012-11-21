<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class RecentVisitorActions extends Actions{
	
	public static function insertRecentVisitor(RecentVisitor $RecentVisitor){
		return parent::MySQLCreateAction('
			INSERT INTO recent_visitor (
				created_datetime,
				visitor_id
			) VALUES (
				:created_datetime,
				:visitor_id
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':visitor_id' => $RecentVisitor->getInteger('visitor_id'),
				':user_id' => $RecentVisitor->getInteger('user_id')
			),
			// which fields are integers
			array(
				':visitor_id',
				':user_id'
			)
		);
	}
	
	public static function selectByRecentVisitorId($user_id){
		// Return one object by primary key selection
		return new RecentVisitor(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				user_id,
				created_datetime,
				visitor_id
			FROM recent_visitor 
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
			'RecentVisitor'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$RecentVisitorCollection = new RecentVisitorCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				visitor_id
			FROM recent_visitor 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'RecentVisitor'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$RecentVisitorCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $RecentVisitorCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('RecentVisitor', new RecentVisitor());
			if($DataObject->getInteger('user_id') > 0){
				$user_ids[] = $DataObject->getInteger('user_id');
			}
		}
		
		$user_ids = array_unique($user_ids);
		
		$RecentVisitorCollection = new RecentVisitorCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				user_id,
				created_datetime,
				visitor_id
			FROM recent_visitor 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'RecentVisitor'
		));
		
		foreach($RecentVisitorCollection->toArray() as $RecentVisitor){
			$array = $DataCollection->getItemsBy('user_id',$RecentVisitor->getInteger('user_id'));
			foreach($array as $DataObject){
				$DataObject->set('RecentVisitor',$RecentVisitor);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateRecentVisitor(RecentVisitor $RecentVisitor){
		return parent::MySQLUpdateAction('
			UPDATE recent_visitor 
			SET visitor_id=:visitor_id
			WHERE user_id=:user_id
			',
			// bind data to sql variables
			array(
				':visitor_id' => $RecentVisitor->getInteger('visitor_id'),
				':user_id' => $RecentVisitor->getInteger('user_id')
			),
			// which fields are integers
			array(
				':visitor_id',
				':user_id'
			)
		);
	}
	
	public static function deleteRecentVisitorById($user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM recent_visitor 
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