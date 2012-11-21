<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class MessageState extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'message_id'=>DataType::NUMBER,
			'Message'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'participant_id'=>DataType::NUMBER,
			'Participant'=>DataType::OBJECT,
			'is_read'=>DataType::BOOLEAN,
			'is_deleted'=>DataType::BOOLEAN
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof MessageState)?$DataObject:new MessageState($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getMessage(){
		return ($this->getObject('Message') instanceof Message)
			?$this->_data['Message']
			:new Message();
	}
	
	public function getParticipant(){
		return ($this->getObject('Participant') instanceof UserAccount)
			?$this->_data['Participant']
			:new UserAccount();
	}
	
}

class MessageStateCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('MessageState');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getMessageStateByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof MessageState)?$return:new MessageState($return->toArray());
	}
	
	public function getMessageStateByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof MessageState)?$return:new MessageState($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}