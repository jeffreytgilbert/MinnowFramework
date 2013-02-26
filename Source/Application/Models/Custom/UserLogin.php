<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UserLogin extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_login_id'=>DataType::NUMBER,
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'unique_identifier'=>DataType::TEXT,
			'user_login_provider_id'=>DataType::NUMBER,
			'UserLoginProvider'=>DataType::OBJECT,
			'serialized_credentials'=>DataType::TEXT,
			'current_failed_attempts'=>DataType::NUMBER,
			'total_failed_attempts'=>DataType::NUMBER,
			'last_failed_attempt'=>DataType::DATETIME,
			'is_verified'=>DataType::BOOLEAN
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UserLogin)?$DataObject:new UserLogin($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['UserAccount']
			:new UserAccount();
	}
	
	public function getUserLoginProvider(){
		return ($this->getObject('UserLoginProvider') instanceof UserLoginProvider)
			?$this->_data['UserLoginProvider']
			:new UserLoginProvider();
	}
	
}

class UserLoginCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UserLogin');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUserLoginByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UserLogin)?$return:new UserLogin($return->toArray());
	}
	
	public function getUserLoginByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UserLogin)?$return:new UserLogin($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}