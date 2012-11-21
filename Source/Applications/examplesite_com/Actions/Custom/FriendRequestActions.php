<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class FriendRequestActions extends Actions{
	
	public static function insertFriendRequest(FriendRequest $FriendRequest){
		return parent::MySQLCreateAction('
			INSERT INTO friend_request (
				created_datetime,
				target_user_id,
				request_code,
				message
			) VALUES (
				:created_datetime,
				:target_user_id,
				:request_code,
				:message
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':target_user_id' => $FriendRequest->getInteger('target_user_id'),
				':request_code' => $FriendRequest->getString('request_code'),
				':message' => $FriendRequest->getString('message'),
				':my_user_id' => $FriendRequest->getInteger('my_user_id')
			),
			// which fields are integers
			array(
				':target_user_id',
				':my_user_id'
			)
		);
	}
	
	public static function selectByFriendRequestId($my_user_id){
		// Return one object by primary key selection
		return new FriendRequest(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				request_code,
				message
			FROM friend_request 
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
			'FriendRequest'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$FriendRequestCollection = new FriendRequestCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				request_code,
				message
			FROM friend_request 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'FriendRequest'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$FriendRequestCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $FriendRequestCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$my_user_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('FriendRequest', new FriendRequest());
			if($DataObject->getInteger('my_user_id') > 0){
				$my_user_ids[] = $DataObject->getInteger('my_user_id');
			}
		}
		
		$my_user_ids = array_unique($my_user_ids);
		
		$FriendRequestCollection = new FriendRequestCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				created_datetime,
				modified_datetime,
				my_user_id,
				target_user_id,
				request_code,
				message
			FROM friend_request 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'FriendRequest'
		));
		
		foreach($FriendRequestCollection->toArray() as $FriendRequest){
			$array = $DataCollection->getItemsBy('my_user_id',$FriendRequest->getInteger('my_user_id'));
			foreach($array as $DataObject){
				$DataObject->set('FriendRequest',$FriendRequest);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateFriendRequest(FriendRequest $FriendRequest){
		return parent::MySQLUpdateAction('
			UPDATE friend_request 
			SET modified_datetime=:modified_datetime,
				target_user_id=:target_user_id,
				request_code=:request_code,
				message=:message
			WHERE my_user_id=:my_user_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':target_user_id' => $FriendRequest->getInteger('target_user_id'),
				':request_code' => $FriendRequest->getString('request_code'),
				':message' => $FriendRequest->getString('message'),
				':my_user_id' => $FriendRequest->getInteger('my_user_id')
			),
			// which fields are integers
			array(
				':target_user_id',
				':my_user_id'
			)
		);
	}
	
	public static function deleteFriendRequestById($my_user_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM friend_request 
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