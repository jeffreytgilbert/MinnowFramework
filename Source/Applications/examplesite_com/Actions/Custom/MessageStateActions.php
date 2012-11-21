<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class MessageStateActions extends Actions{
	
	public static function insertMessageState(MessageState $MessageState){
		return parent::MySQLCreateAction('
			INSERT INTO message_state (
				created_datetime,
				participant_id,
				is_read,
				is_deleted
			) VALUES (
				:created_datetime,
				:participant_id,
				:is_read,
				:is_deleted
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RIGHT_NOW_GMT,
				':participant_id' => $MessageState->getInteger('participant_id'),
				':is_read' => $MessageState->getBoolean('is_read'),
				':is_deleted' => $MessageState->getBoolean('is_deleted'),
				':message_id' => $MessageState->getInteger('message_id')
			),
			// which fields are integers
			array(
				':participant_id',
				':message_id'
			)
		);
	}
	
	public static function selectByMessageStateId($message_id){
		// Return one object by primary key selection
		return new MessageState(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				message_id,
				created_datetime,
				modified_datetime,
				participant_id,
				is_read,
				is_deleted
			FROM message_state 
			WHERE message_id=:message_id',
			// bind data to sql variables
			array(
				':message_id' => (int)$message_id
			),
			// which fields are integers
			array(
				':message_id'
			),
			// return as this object collection type
			'MessageState'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$MessageStateCollection = new MessageStateCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				message_id,
				created_datetime,
				modified_datetime,
				participant_id,
				is_read,
				is_deleted
			FROM message_state 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'MessageState'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$MessageStateCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $MessageStateCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$message_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('MessageState', new MessageState());
			if($DataObject->getInteger('message_id') > 0){
				$message_ids[] = $DataObject->getInteger('message_id');
			}
		}
		
		$message_ids = array_unique($message_ids);
		
		$MessageStateCollection = new MessageStateCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				message_id,
				created_datetime,
				modified_datetime,
				participant_id,
				is_read,
				is_deleted
			FROM message_state 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'MessageState'
		));
		
		foreach($MessageStateCollection->toArray() as $MessageState){
			$array = $DataCollection->getItemsBy('message_id',$MessageState->getInteger('message_id'));
			foreach($array as $DataObject){
				$DataObject->set('MessageState',$MessageState);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateMessageState(MessageState $MessageState){
		return parent::MySQLUpdateAction('
			UPDATE message_state 
			SET modified_datetime=:modified_datetime,
				participant_id=:participant_id,
				is_read=:is_read,
				is_deleted=:is_deleted
			WHERE message_id=:message_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RIGHT_NOW_GMT,
				':participant_id' => $MessageState->getInteger('participant_id'),
				':is_read' => $MessageState->getBoolean('is_read'),
				':is_deleted' => $MessageState->getBoolean('is_deleted'),
				':message_id' => $MessageState->getInteger('message_id')
			),
			// which fields are integers
			array(
				':participant_id',
				':message_id'
			)
		);
	}
	
	public static function deleteMessageStateById($message_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM message_state 
			WHERE message_id=:message_id',
			// bind data to sql variables
			array(
				':message_id' => (int)$message_id
			),
			// which fields are integers
			array(
				':message_id'
			)
		);
	}

}