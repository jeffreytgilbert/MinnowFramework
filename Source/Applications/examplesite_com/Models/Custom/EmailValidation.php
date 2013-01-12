<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class EmailValidation extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'User'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'code'=>DataType::TEXT,
			'email_address'=>DataType::TEXT
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof EmailValidation)?$DataObject:new EmailValidation($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUser(){
		return ($this->getObject('User') instanceof User)
			?$this->_data['User']
			:new User();
	}
	
}

class EmailValidationCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('EmailValidation');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getEmailValidationByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof EmailValidation)?$return:new EmailValidation($return->toArray());
	}
	
	public function getEmailValidationByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof EmailValidation)?$return:new EmailValidation($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}