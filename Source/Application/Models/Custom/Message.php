<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Message extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'message_id'=>DataType::NUMBER,
			'conversation_id'=>DataType::NUMBER,
			'Conversation'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'sender_id'=>DataType::NUMBER,
			'Sender'=>DataType::OBJECT,
			'body'=>DataType::TEXT,
			'location'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Message)?$DataObject:new Message($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getConversation(){
		return ($this->getObject('Conversation') instanceof Conversation)
			?$this->_data['Conversation']
			:new Conversation();
	}
	
	public function getSender(){
		return ($this->getObject('Sender') instanceof UserAccount)
			?$this->_data['Sender']
			:new UserAccount();
	}
	
}

class MessageCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Message');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getMessageByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Message)?$return:new Message($return->toArray());
	}
	
	public function getMessageByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Message)?$return:new Message($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}