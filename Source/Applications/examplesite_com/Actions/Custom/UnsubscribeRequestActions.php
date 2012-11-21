<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class UnsubscribeRequestActions extends Actions{
	
	public static function insertUnsubscribeRequest(UnsubscribeRequest $UnsubscribeRequest){
		return parent::MySQLCreateAction('
			INSERT INTO unsubscribe_request (
				email,
				ip,
				created_datetime
			) VALUES (
				:email,
				:ip,
				:created_datetime
			)',
			// bind data to sql variables
			array(
				':email' => $UnsubscribeRequest->getString('email'),
				':ip' => $UnsubscribeRequest->getString('ip'),
				':created_datetime' => RIGHT_NOW_GMT,
				':unsubscribe_request_id' => $UnsubscribeRequest->getInteger('unsubscribe_request_id')
			),
			// which fields are integers
			array(
				
				':unsubscribe_request_id'
			)
		);
	}
	
	public static function selectByUnsubscribeRequestId($unsubscribe_request_id){
		// Return one object by primary key selection
		return new UnsubscribeRequest(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				unsubscribe_request_id,
				email,
				ip,
				created_datetime
			FROM unsubscribe_request 
			WHERE unsubscribe_request_id=:unsubscribe_request_id',
			// bind data to sql variables
			array(
				':unsubscribe_request_id' => (int)$unsubscribe_request_id
			),
			// which fields are integers
			array(
				':unsubscribe_request_id'
			),
			// return as this object collection type
			'UnsubscribeRequest'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$UnsubscribeRequestCollection = new UnsubscribeRequestCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				unsubscribe_request_id,
				email,
				ip,
				created_datetime
			FROM unsubscribe_request 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UnsubscribeRequest'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$UnsubscribeRequestCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $UnsubscribeRequestCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$unsubscribe_request_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('UnsubscribeRequest', new UnsubscribeRequest());
			if($DataObject->getInteger('unsubscribe_request_id') > 0){
				$unsubscribe_request_ids[] = $DataObject->getInteger('unsubscribe_request_id');
			}
		}
		
		$unsubscribe_request_ids = array_unique($unsubscribe_request_ids);
		
		$UnsubscribeRequestCollection = new UnsubscribeRequestCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				unsubscribe_request_id,
				email,
				ip,
				created_datetime
			FROM unsubscribe_request 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'UnsubscribeRequest'
		));
		
		foreach($UnsubscribeRequestCollection->toArray() as $UnsubscribeRequest){
			$array = $DataCollection->getItemsBy('unsubscribe_request_id',$UnsubscribeRequest->getInteger('unsubscribe_request_id'));
			foreach($array as $DataObject){
				$DataObject->set('UnsubscribeRequest',$UnsubscribeRequest);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateUnsubscribeRequest(UnsubscribeRequest $UnsubscribeRequest){
		return parent::MySQLUpdateAction('
			UPDATE unsubscribe_request 
			SET email=:email,
				ip=:ip
			WHERE unsubscribe_request_id=:unsubscribe_request_id
			',
			// bind data to sql variables
			array(
				':email' => $UnsubscribeRequest->getString('email'),
				':ip' => $UnsubscribeRequest->getString('ip'),
				':unsubscribe_request_id' => $UnsubscribeRequest->getInteger('unsubscribe_request_id')
			),
			// which fields are integers
			array(
				
				':unsubscribe_request_id'
			)
		);
	}
	
	public static function deleteUnsubscribeRequestById($unsubscribe_request_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM unsubscribe_request 
			WHERE unsubscribe_request_id=:unsubscribe_request_id',
			// bind data to sql variables
			array(
				':unsubscribe_request_id' => (int)$unsubscribe_request_id
			),
			// which fields are integers
			array(
				':unsubscribe_request_id'
			)
		);
	}

}