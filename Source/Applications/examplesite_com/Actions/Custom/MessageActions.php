<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class MessageActions extends Actions{
	
	public static function insertMessage(Message $Message){
		return parent::MySQLCreateAction('
			INSERT INTO message (
				conversation_id,
				created_datetime,
				sender_id,
				body,
				location
			) VALUES (
				:conversation_id,
				:created_datetime,
				:sender_id,
				:body,
				:location
			)',
			// bind data to sql variables
			array(
				':conversation_id' => $Message->getInteger('conversation_id'),
				':created_datetime' => RIGHT_NOW_GMT,
				':sender_id' => $Message->getInteger('sender_id'),
				':body' => $Message->getString('body'),
				':location' => $Message->getString('location'),
				':message_id' => $Message->getInteger('message_id')
			),
			// which fields are integers
			array(
				':conversation_id',
				':sender_id',
				':message_id'
			)
		);
	}
	
	public static function selectByMessageId($message_id){
		// Return one object by primary key selection
		return new Message(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				message_id,
				conversation_id,
				created_datetime,
				modified_datetime,
				sender_id,
				body,
				location
			FROM message 
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
			'Message'
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$MessageCollection = new MessageCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				message_id,
				conversation_id,
				created_datetime,
				modified_datetime,
				sender_id,
				body,
				location
			FROM message 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Message'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$MessageCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $MessageCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$message_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Message', new Message());
			if($DataObject->getInteger('message_id') > 0){
				$message_ids[] = $DataObject->getInteger('message_id');
			}
		}
		
		$message_ids = array_unique($message_ids);
		
		$MessageCollection = new MessageCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				message_id,
				conversation_id,
				created_datetime,
				modified_datetime,
				sender_id,
				body,
				location
			FROM message 
			',
			// bind data to sql variables
			array(
			),
			// which fields are integers
			array(
			),
			'Message'
		));
		
		foreach($MessageCollection->toArray() as $Message){
			$array = $DataCollection->getItemsBy('message_id',$Message->getInteger('message_id'));
			foreach($array as $DataObject){
				$DataObject->set('Message',$Message);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateMessage(Message $Message){
		return parent::MySQLUpdateAction('
			UPDATE message 
			SET conversation_id=:conversation_id,
				modified_datetime=:modified_datetime,
				sender_id=:sender_id,
				body=:body,
				location=:location
			WHERE message_id=:message_id
			',
			// bind data to sql variables
			array(
				':conversation_id' => $Message->getInteger('conversation_id'),
				':modified_datetime' => RIGHT_NOW_GMT,
				':sender_id' => $Message->getInteger('sender_id'),
				':body' => $Message->getString('body'),
				':location' => $Message->getString('location'),
				':message_id' => $Message->getInteger('message_id')
			),
			// which fields are integers
			array(
				':conversation_id',
				':sender_id',
				':message_id'
			)
		);
	}
	
	public static function deleteMessageById($message_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM message 
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