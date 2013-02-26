<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

final class ConversationParticipantActions extends Actions{
	
	public static function insertConversationParticipant(ConversationParticipant $ConversationParticipant){
		return parent::MySQLCreateAction('
			INSERT INTO conversation_participant (
				created_datetime,
				participant_id,
				is_deleted
			) VALUES (
				:created_datetime,
				:participant_id,
				:is_deleted
			)',
			// bind data to sql variables
			array(
				':created_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':participant_id' => $ConversationParticipant->getInteger('participant_id'),
				':is_deleted' => $ConversationParticipant->getBoolean('is_deleted'),
				':conversation_id' => $ConversationParticipant->getInteger('conversation_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':participant_id',
				':is_deleted',
				':conversation_id'
			)
		);
	}
	
	public static function selectByConversationParticipantId($conversation_id){
		// Return one object by primary key selection
		return new ConversationParticipant(parent::MySQLReadReturnSingleResultAsArrayAction('
			SELECT 
				conversation_id,
				created_datetime,
				modified_datetime,
				participant_id,
				is_deleted
			FROM conversation_participant 
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
		$ConversationParticipantCollection = new ConversationParticipantCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				conversation_id,
				created_datetime,
				modified_datetime,
				participant_id,
				is_deleted
			FROM conversation_participant 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'ConversationParticipant'
		,
			$PagingConfig
		));
		
		if($PagingConfig instanceof PagingConfig){
			$ConversationParticipantCollection->setPaginationData($PagingConfig->getStart(), $PagingConfig->getLimit(), self::MySQLTotalRows());
		}
		
		return $ConversationParticipantCollection;
	}
	
	// Placeholder function for creating lists and linking objects.
	// This method makes object chaining and fast querying possible by using "IN" in mysql to pull down relative data
	// and bind objects to collections
	public static function selectListForDataCollection(DataCollection $DataCollection){
		$conversation_ids = array();
		foreach($DataCollection->toArray() as $DataObject){
			$DataObject->set('ConversationParticipant', new ConversationParticipant());
			if($DataObject->getInteger('conversation_id') > 0){
				$conversation_ids[] = $DataObject->getInteger('conversation_id');
			}
		}
		
		$conversation_ids = array_unique($conversation_ids);
		
		$ConversationParticipantCollection = new ConversationParticipantCollection(parent::MySQLReadReturnArrayOfObjectsAction('
			SELECT 
				conversation_id,
				created_datetime,
				modified_datetime,
				participant_id,
				is_deleted
			FROM conversation_participant 
			',
			// bind data to sql variables
			array(
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
			),
			'ConversationParticipant'
		));
		
		foreach($ConversationParticipantCollection->toArray() as $ConversationParticipant){
			$array = $DataCollection->getObjectByFieldValue('conversation_id',$ConversationParticipant->getInteger('conversation_id'));
			foreach($array as $DataObject){
				$DataObject->set('ConversationParticipant',$ConversationParticipant);
			}
		}
		
		return $DataCollection;
	}
	
	public static function updateConversationParticipant(ConversationParticipant $ConversationParticipant){
		return parent::MySQLUpdateAction('
			UPDATE conversation_participant 
			SET modified_datetime=:modified_datetime,
				participant_id=:participant_id,
				is_deleted=:is_deleted
			WHERE conversation_id=:conversation_id
			',
			// bind data to sql variables
			array(
				':modified_datetime' => RuntimeInfo::instance()->now()->getMySQLFormat('datetime'),
				':participant_id' => $ConversationParticipant->getInteger('participant_id'),
				':is_deleted' => $ConversationParticipant->getBoolean('is_deleted'),
				':conversation_id' => $ConversationParticipant->getInteger('conversation_id')
			),
			// which fields are non-string, unquoted types (boolean, float, int, decimal, etc)
			array(
				':participant_id',
				':conversation_id'
			)
		);
	}
	
	public static function deleteConversationParticipantById($conversation_id){
		return parent::MySQLUpdateAction('
			DELETE 
			FROM conversation_participant 
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