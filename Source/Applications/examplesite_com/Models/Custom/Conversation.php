<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class Conversation extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'conversation_id'=>DataType::NUMBER,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'creator_id'=>DataType::NUMBER,
			'Creator'=>DataType::OBJECT,
			'last_sender_id'=>DataType::NUMBER,
			'LastSender'=>DataType::OBJECT,
			'last_message'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof Conversation)?$DataObject:new Conversation($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getCreator(){
		return ($this->getObject('Creator') instanceof UserAccount)
			?$this->_data['Creator']
			:new UserAccount();
	}
	
	public function getLastSender(){
		return ($this->getObject('LastSender') instanceof UserAccount)
			?$this->_data['LastSender']
			:new UserAccount();
	}
	
}

class ConversationCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('Conversation');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getConversationByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof Conversation)?$return:new Conversation($return->toArray());
	}
	
	public function getConversationByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof Conversation)?$return:new Conversation($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}