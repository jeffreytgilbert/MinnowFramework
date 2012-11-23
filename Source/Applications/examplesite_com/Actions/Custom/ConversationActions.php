<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class ConversationActions extends Actions{
	
	public static function insertConversation(Conversation $Conversation){
		return parent::MySQLCreateAction('
			INSERT INTO conversation (
				created_datetime,
				creator_id,
				last_sender_id,
				last_message
			) VALUES (
				:created_datetime,
				:creator_id,
				:last_sender_id,
				:last_message
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':creator_id' => $Conversation->getInteger('creator_id'),
				':last_sender_id' => $Conversation->getInteger('last_sender_id'),
				':last_message' => $Conversation->getString('last_message'),
				':conversation_id' => $Conversation->getInteger('conversation_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':creator_id',
				':last_sender_id',
				':conversation_id'
			)
		);
	}
	
	public static function selectByConversationId($conversation_id){
		// Return one object by primary key selection
		return new Conversation(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				conversation_id,
				created_datetime,
				modified_datetime,
				creator_id,
				last_sender_id,
				last_message
			FROM conversation 
			WHERE conversation_id=:conversation_id',
			// bind data to sql variables
			array(
				':conversation_id' => (int)$conversation_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':conversation_id'
			)
		));
	}
	
	public static function selectList(PagingConfig $PagingConfig=null){
		// Return an object collection
		$ConversationCollection = new ConversationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				conversation_id,
				created_datetime,
				modified_datetime,
				creator_id,
				last_sender_id,
				last_message
			FROM conversation 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Conversation'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$ConversationCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $ConversationCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$conversation_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('Conversation', new Conversation());
			if($DataObject->getInteger('conversation_id') > 0){
				$conversation_ids[] = $DataObject->getInteger('conversation_id');
			}
		}
		
		$conversation_ids = array_unique($conversation_ids);
		
		$ConversationCollection = new ConversationCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				conversation_id,
				created_datetime,
				modified_datetime,
				creator_id,
				last_sender_id,
				last_message
			FROM conversation 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'Conversation'
		));
		
		foreach($ConversationCollection->toArray() as $Conversation){
			$array = $DataCollection->getObjectByFieldValue('conversation_id',$Conversation->getInteger('conversation_id'));
			foreach($array as $DataObject){
				$DataObject->set('Conversation',$Conversation);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateConversation(Conversation $Conversation){
		return parent::MySQLUpdateAction('
			UPDATE conversation 
			SET modified_datetime=:modified_datetime,
				creator_id=:creator_id,
				last_sender_id=:last_sender_id,
				last_message=:last_message
			WHERE conversation_id=:conversation_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':creator_id' => $Conversation->getInteger('creator_id'),
				':last_sender_id' => $Conversation->getInteger('last_sender_id'),
				':last_message' => $Conversation->getString('last_message'),
				':conversation_id' => $Conversation->getInteger('conversation_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':creator_id',
				':last_sender_id',
				':conversation_id'
			)
		);
	}
	
	public static function deleteConversationById($conversation_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM conversation 
			WHERE conversation_id=:conversation_id',
			// bind data to sql variables
			array(
				':conversation_id' => (int)$conversation_id
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':conversation_id'
			)
		);
	}

}