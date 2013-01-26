<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UserPasswordResetRequest extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'User'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'reset_code'=>DataType::TEXT,
			'result'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UserPasswordResetRequest)?$DataObject:new UserPasswordResetRequest($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUser(){
		return ($this->getObject('User') instanceof User)
			?$this->_data['User']
			:new User();
	}
	
}

class UserPasswordResetRequestCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UserPasswordResetRequest');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUserPasswordResetRequestByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UserPasswordResetRequest)?$return:new UserPasswordResetRequest($return->toArray());
	}
	
	public function getUserPasswordResetRequestByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UserPasswordResetRequest)?$return:new UserPasswordResetRequest($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}