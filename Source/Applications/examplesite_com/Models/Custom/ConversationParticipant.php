<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class ConversationParticipant extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'conversation_id'=>DataType::NUMBER,
			'Conversation'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'participant_id'=>DataType::NUMBER,
			'Participant'=>DataType::OBJECT,
			'is_deleted'=>DataType::BOOLEAN
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof ConversationParticipant)?$DataObject:new ConversationParticipant($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getConversation(){
		return ($this->getObject('Conversation') instanceof Conversation)
			?$this->_data['Conversation']
			:new Conversation();
	}
	
	public function getParticipant(){
		return ($this->getObject('Participant') instanceof UserAccount)
			?$this->_data['Participant']
			:new UserAccount();
	}
	
}

class ConversationParticipantCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('ConversationParticipant');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getConversationParticipantByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof ConversationParticipant)?$return:new ConversationParticipant($return->toArray());
	}
	
	public function getConversationParticipantByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof ConversationParticipant)?$return:new ConversationParticipant($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}