<?php

/*
 * This work is licensed under the Creative Commons Attribution-ShareAlike 3.0 Unported License. To view a copy of this license, visit http://creativecommons.org/licenses/by-sa/3.0/.
 */

class UserLoginHistory extends DataObject{
	public function __construct(Array $data=array()){
		$this->addAllowedData(array(
			'user_id'=>DataType::NUMBER,
			'UserAccount'=>DataType::OBJECT,
			'created_datetime'=>DataType::DATETIME,
			'modified_datetime'=>DataType::DATETIME,
			'login'=>DataType::TEXT,
			'user_agent'=>DataType::TEXT,
			'ip'=>DataType::TEXT,
			'proxy'=>DataType::TEXT,
			'description'=>DataType::TEXT,
			'success'=>DataType::NUMBER
		),true);
		parent::__construct($data);
	}

	public static function cast(DataObject $DataObject){
		return ($DataObject instanceof UserLoginHistory)?$DataObject:new UserLoginHistory($DataObject->toArray());
	}
	
	// Static typed child methods for autocomplete on object
	
	
	public function getUserAccount(){
		return ($this->getObject('UserAccount') instanceof UserAccount)
			?$this->_data['User']
			:new UserAccount();
	}
	
}

class UserLoginHistoryCollection extends DataCollection{
	public function __construct(Array $array_of_objects=null){
		$this->setCollectionType('UserLoginHistory');
		parent::__construct($array_of_objects);
	}
	
	// Add autocomplete helper methods to the only two ModelCollection methods that return an object
	
	public function getUserLoginHistoryByFieldValue($column_name, $data_value, $return_blank_object_if_null=true){
		$return = parent::getObjectByFieldValue($column_name, $data_value, $return_blank_object_if_null);
		return ($return instanceof UserLoginHistory)?$return:new UserLoginHistory($return->toArray());
	}
	
	public function getUserLoginHistoryByIndex($index, $return_blank_object_if_null=true){
		$return = parent::getObjectByIndex($index, $return_blank_object_if_null);
		return ($return instanceof UserLoginHistory)?$return:new UserLoginHistory($return->toArray());
	}
	
	// Static typed child methods for autocomplete on collection
}